<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Pricelist;
use App\Models\Order;
use App\Models\payment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentAdd extends Component
{
    public $SubCategoryOrderid = [];
    public $Order = [];
    public $isOpen = false;
    public $data = [];
    public $step = 1;
    public $order_id;
    public $availableSubcategories = [];
    public $selected_subcategory_id;
    public $filteredSubCategories = [];
    public $selectedpayment;
    public $order;
    public $payment;

    public function mount()
    {
      $this->Order = Order::where('isActive', 1)
        ->whereIn('order_id', function ($query) {
            $query->select('order_id')
                ->from('orders')
                ->groupBy('order_id')
                ->havingRaw('COUNT(*) > (
                    SELECT COUNT(*) FROM payments 
                    WHERE payments.order_id = orders.order_id
                )');
        })
        ->get();
    }

    public function render()
    {
        return view('livewire.payment-add');

        
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

    public function updatedOrderId($value)
    {
        // Get subcategories from the order
        $subcatIds = Order::where('order_id', $value)->pluck('subcategory_id')->unique();

        // Get already paid subcategory_ids for this order
        $paidSubcategoryIds = payment::where('order_id', $value)->pluck('subcategory_id')->toArray();

        // Filter subcategories that haven't been paid yet
        $this->filteredSubCategories = SubCategory::whereIn('subcategory_id', $subcatIds)
            ->whereNotIn('subcategory_id', $paidSubcategoryIds)
            ->get();

        $this->selected_subcategory_id = null;
    }

    public function updatedSelectedSubcategoryId($value)
    {
        $order = Order::where('order_id', $this->order_id)
            ->where('subcategory_id', $value)
            ->first();

        if ($order) {
            $this->order = $order; // Assign for later use
            $this->data = [
                'subcategory_id' => $value,
                'date' => $order->deadline,
                'name' => $order->name,
                'contact_no' => $order->contact_no,
                'address' => $order->address,
                'jo_number' => $order->jo_number,
                'total' => $order->total,
                'balance' => $order->balance,
                'amount' => $order->amount,
                'status' => $order->status,
                'qty' => $order->qty,
                'price' => $order->price,
            ];
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedpayment', 'data.total', 'data.payment'])) {
            $this->recalculateBalance();
        }
    }

   private function recalculateBalance()
{
    $total = floatval($this->data['total'] ?? 0);
    $payment = floatval($this->data['payment'] ?? 0);

    if ($this->selectedpayment === 'Full Payment') {
        $this->data['payment'] = $total;
        $this->data['balance'] = 0;
        $this->data['payment_status'] = 'Paid';
    } elseif ($this->selectedpayment === 'Downpayment') {
        // Respect the payment entered
        $this->data['balance'] = $total - $payment;
        $this->data['payment_status'] = 'Partial';
    } elseif ($this->selectedpayment === 'Unpaid') {
        $this->data['payment'] = 0;
        $this->data['balance'] = $total;
        $this->data['payment_status'] = 'Unpaid';
    }
}


    public function updatedData($value, $key)
    {
        if ($key === 'payment') {
            $this->recalculateBalance();
        }
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
                'data.subcategory_id' => 'required',
                'data.name' => 'required',
                'data.address' => 'nullable|string|max:255',
                'data.amount' => 'required|numeric',
                'data.total' => 'required|numeric',
                'data.balance' => 'required|numeric',
                'data.jo_number' => 'nullable|string|max:255',
                'data.payment' => 'required|numeric|min:0',
                'data.payment_method' => 'nullable|string|max:255',
                'data.reference_number' => 'nullable|string|max:255',
                'data.payment_date' => 'nullable|date_format:Y-m-d\TH:i',
                'data.status' => 'nullable|string|max:50',
                'data.payment_status' => 'nullable|string|max:50',
                'data.remarks' => 'nullable|string|max:500',
            ]);

            $count = payment::where('order_id', $this->order_id)->count() + 1;
            $payment_id = 'PYMT-' . $this->order_id . '-' . str_pad($count, 2, '0', STR_PAD_LEFT);
            $paymentDate = isset($this->data['payment_date'])
            ? Carbon::parse($this->data['payment_date'])->format('Y-m-d H:i:s')
            : now()->format('Y-m-d H:i:s');

            payment::create([
                'payment_id' => $payment_id,
                'order_id' => $this->order_id,
                'subcategory_id' => $this->data['subcategory_id'],
                'jo_number' => $this->data['jo_number'] ?? null,
                'name' => $this->data['name'],
                'address' => $this->data['address'] ?? null,
                'amount' => $this->data['amount'],
                'total' => $this->data['total'],
                'balance' => $this->data['balance'],
                'payment' => $this->data['payment'],
                'payment_method' => $this->data['payment_method'] ?? null,
                'reference_number' => $this->data['reference_number'] ?? null,
                'payment_date' => $paymentDate,
                'payment_status' => $this->data['payment_status'] ?? null,
                'remarks' => $this->data['remarks'] ?? '',
                'status' => $this->data['status'] ?? '',
                'isActive' => true,
                'service_by' => Auth::user()->username,
            ]);

            $order = Order::where('order_id', $this->order_id)
                ->where('subcategory_id', $this->data['subcategory_id'])
                ->first();

            if ($order) {
                $order->update([
                    'status' => $this->data['status'] ?? $order->status,
                    'remarks' => $this->data['remarks'] ?? $order->remarks,
                ]);
            }

            $this->emit('refreshComponent');
            $this->reset(['data', 'order_id', 'selected_subcategory_id']);
            $this->isOpen = false;
            $this->step = 1;

            session()->flash('messageInsert', 'Order is successfully paid!');
        } catch (\Exception $e) {
            session()->flash('errorInsert', 'Failed to save order: ' . $e->getMessage());
        }
    }
}
