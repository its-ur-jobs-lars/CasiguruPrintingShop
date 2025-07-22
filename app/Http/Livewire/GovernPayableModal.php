<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\payment;
use App\Models\GovernPayable;
use Illuminate\Support\Facades\Auth;

class GovernPayableModal extends Component
{
    public $isOpen = false;
    public $order_id;
    public $selectedpayment;
    public $step = 1;
    public $Order;
    public $data = [];

    public function mount()
    {
        $this->data = [
            'order_id' => '',
            'po_number' => '',
            'name' => '',
            'title' => '',
            'total' => 0.00,
            'payment_date' => null,
            'process_date' => null,
            'document_status' => '',
            'payment' => 0.00,
            'payment_method' => '',
            'payment_balance' => 0.00,
            'payment_status' => 'unpaid',
            'remarks' => '',
            'status' => 'pending',
            'added_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ];

        $this->Order = Order::where('isActive', 1)
            ->where('customer_type', 'Government')
            ->get()
            ->filter(function ($order) {
                $latestPayment = Payment::where('order_id', $order->order_id)
                    ->latest('id')
                    ->first();

                if (!$latestPayment) {
                    return true; // No payment yet
                }

                return strtolower($latestPayment->payment_status) !== 'paid' || floatval($latestPayment->balance) > 0.00;
            })
            ->values();
          }

    public function updatedSelectedpayment($value)
    {
        // Payment logic depending on selected type
        if ($value === 'Downpayment') {
            $this->data['payment'] = '';
        }

        if ($value === 'Full Payment') {
            $this->data['payment'] = $this->data['total'] ?? 0;
        }

        $this->recalculateBalance();
    }

    public function updatedOrderId($value)
    {
        $order = Order::where('order_id', $value)->first();
        $latestPayment = payment::where('order_id', $value)->latest('id')->first();

        if ($order) {
            $this->data['order_id'] = $order->order_id;
            $this->data['name'] = $order->name;
            $this->data['total'] = $order->total;
            $this->data['remarks'] = $order->remarks;
            $this->data['status'] = $order->status;
        } else {
            $this->data['name'] = '';
            $this->data['title'] = '';
            $this->data['total'] = 0;
            $this->data['remarks'] = '';
            $this->data['status'] = '';
        }

        if ($latestPayment) {
            $this->data['payment_method'] = $latestPayment->payment_method;
            $this->data['payment'] = $latestPayment->amount;
            $this->data['payment_date'] = $latestPayment->payment_date ? $latestPayment->payment_date->format('Y-m-d') : null;
            $this->data['payment_balance'] = $latestPayment->balance;
            $this->data['payment_status'] = $latestPayment->payment_status;
        } else {
            $this->data['payment_method'] = '';
            $this->data['payment'] = 0;
            $this->data['payment_date'] = null;
            $this->data['payment_balance'] = 0;
            $this->data['payment_status'] = '';
        }
    }

    public function recalculateBalance()
    {
        $total = floatval($this->data['total'] ?? 0);
        $payment = floatval($this->data['payment'] ?? 0);
        $this->data['payment_balance'] = $total - $payment;
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->step = 1;
    }

    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
    public function nextStep2() { $this->step = 3; }
    public function previousStep1() { $this->step = 2; }

    public function save()
{
    try {
        $this->validate([
            'data.order_id' => 'required|string',
            'data.po_number' => 'nullable|string|max:255',
            'data.name' => 'required|string|max:255',
            'data.title' => 'nullable|string|max:255',
            'data.payment_date' => 'nullable|date|max:255',
            'data.process_date' => 'nullable|date|max:255',
            'data.total' => 'required|numeric',
            'data.payment_method' => 'required|string|max:100',
            'data.payment_balance' => 'required|numeric',
            'data.payment_status' => 'required|string|max:50',
            'data.remarks' => 'nullable|string|max:255',
            'data.status' => 'required|string|max:50',
        ]);

        GovernPayable::create([
            'order_id' => $this->data['order_id'],
            'po_number' => $this->data['po_number'] ?? null,
            'name' => $this->data['name'],
            'title' => $this->data['title'] ?? null,
            'total' => $this->data['total'],
            'payment_date' => $this->data['payment_date'] ?? null,
            'process_date' => $this->data['process_date'] ?? null,
            'payment' => $this->data['payment'],
            'payment_method' => $this->data['payment_method'],
            'payment_balance' => $this->data['payment_balance'],
            'payment_status' => $this->data['payment_status'],
            'remarks' => $this->data['remarks'] ?? '',
            'document_status' => $this->data['document_status'] ?? '',
            'status' => $this->data['status'],
            'added_by' => Auth::user()->username,
        ]);

        $this->emit('refreshComponent');
        $this->reset(['data']);
        $this->isOpen = false;
        $this->step = 1;

        session()->flash('messageInsert', 'Government payable successfully created!');
    } catch (\Exception $e) {
        session()->flash('errorInsert', 'Failed to save government payable: ' . $e->getMessage());
    }
}
    

    public function render()
    {
        return view('livewire.govern-payable-modal');
    }
}
