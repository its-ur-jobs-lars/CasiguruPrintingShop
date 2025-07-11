<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\payment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentAdd extends Component
{
    public $Order = [];
    public $isOpen = false;
    public $data = [];
    public $step = 1;
    public $order_id;
    public $selectedpayment;

    public function mount()
    {
        $this->data['payment_method'] = '';

        $this->data = [
            'order_id' => '',
            'name' => '',
            'contact_no' => '',
            'address' => '',
            'jo_number' => '',
            'total' => 0,
            'balance' => 0,
            'amount' => 0,
            'status' => '',
            'qty' => 0,
            'payment' => 0,
            'payment_method' => '',
            'reference_number' => '',
            'payment_date' => now()->format('Y-m-d\TH:i'),
            'payment_status' => '',
            'remarks' => '',
        ];
        $orders = Order::where('isActive', 1)->get();

        $ordersWithRemainingBalance = $orders->filter(function ($order) {
            $totalPaid = payment::where('order_id', $order->order_id)
                ->where('subcategory_id', $order->subcategory_id)
                ->sum('payment');

            return $order->total > $totalPaid;
        });

        $this->Order = $ordersWithRemainingBalance->values();
    }

    public function getSelectedOrderName()
    {
        $order = Order::where('order_id', $this->order_id)->first();
        return $order ? $order->name : '';
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

private function getLatestBalance($order_id, $subcategory_id, $order_total)
{
    $lastPayment = payment::where('order_id', $order_id)
        ->where('subcategory_id', $subcategory_id)
        ->orderByDesc('payment_date')
        ->orderByDesc('id')
        ->first();

    if ($lastPayment) {
        return $lastPayment->balance;
    }
    return $order_total;
}

    public function updatedSelectedpayment($value)
{
    // If Downpayment is selected, clear the default payment so the user can enter it
    if ($value === 'Downpayment') {
        $this->data['payment'] = '';
    }

    // If Full Payment is selected, autofill the total as payment
    if ($value === 'Full Payment') {
        $this->data['payment'] = $this->data['total'] ?? 0;
    }

    // Recalculate balance immediately
    $this->recalculateBalance();
}


public function updatedOrderId($value)
{
    $orders = Order::where('order_id', $value)->get();

    if ($orders->isNotEmpty()) {
        $firstOrder = $orders->first();

        $hasPreviousPayment = payment::where('order_id', $value)->exists();
        $grandTotal = $firstOrder->total;
        $payment = $grandTotal;

        if ($hasPreviousPayment) {
            $latestPayment = payment::where('order_id', $value)
                ->latest('payment_date')
                ->latest('id')
                ->first();

            $grandTotal = $latestPayment->balance ?? $grandTotal;
            $payment = 0; // temporarily 0, will be replaced if user selects "Full Payment"
        }

        $totalPaid = payment::where('order_id', $value)->sum('payment');
        $remainingBalance = max($grandTotal - $totalPaid, 0);
        $amount = $orders->sum('amount');

        $this->data = [
            'order_id' => $value,
            'date' => $firstOrder->deadline,
            'name' => $firstOrder->name,
            'contact_no' => $firstOrder->contact_no,
            'address' => $firstOrder->address,
            'jo_number' => $firstOrder->jo_number,
            'total' => $grandTotal,
            'balance' => $remainingBalance,
            'amount' => $amount,
            'status' => $firstOrder->status,
            'qty' => $orders->sum('qty'),
            'payment' => $payment,
            'payment_status' => $remainingBalance == 0 ? 'Paid' : 'Partial',
        ];
    }
}



    public function updated($property)
    {
        if (in_array($property, ['data.total', 'data.payment'])) {
            $this->recalculateBalance();
        }
    }

    public function updatedData($value, $key)
    {
        if ($key === 'payment') {
            $this->recalculateBalance();
        }
    }

    private function recalculateBalance()
    {
        $total = floatval($this->data['total'] ?? 0);
        $payment = floatval($this->data['payment'] ?? 0);
        $this->data['balance'] = max(round($total - $payment, 2), 0);

        $this->data['payment_status'] = $this->data['balance'] == 0 ? 'Paid' : 'Partial';
    }

    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
    public function nextStep2() { $this->step = 3; }
    public function previousStep1() { $this->step = 2; }

 public function save()
{
    try {
        $rules = [
            'data.order_id' => 'required',
            'data.name' => 'required',
            'data.address' => 'nullable|string|max:255',
            'data.amount' => 'required|numeric',
            'data.total' => 'required|numeric',
            'data.payment' => 'required|numeric|min:0',
            'data.payment_method' => 'required|string|max:255',
            'data.payment_date' => 'nullable|date_format:Y-m-d\\TH:i',
            'data.status' => 'nullable|string|max:50',
            'data.payment_status' => 'nullable|string|max:50',
            'data.remarks' => 'nullable|string|max:500',
        ];

        // Extra fields based on payment method
        if ($this->data['payment_method'] === 'GCash') {
            $rules['data.gcash_number'] = 'required|string|max:20';
            $rules['data.gcash_account_name'] = 'required|string|max:100';
            $rules['data.reference_number'] = 'required|string|max:100';
        } elseif ($this->data['payment_method'] === 'Cash') {
            $rules['data.reference_number'] = 'required|string|max:100'; // optional or required based on your logic
        } elseif ($this->data['payment_method'] === 'Bank Transfer') {
            $rules['data.bank_name'] = 'required|string|max:100';
            $rules['data.reference_number'] = 'required|string|max:100';
        } elseif ($this->data['payment_method'] === 'Cheque') {
            $rules['data.cheque_number'] = 'required|string|max:50';
            $rules['data.cheque_date'] = 'required|date';
            $rules['data.bank_name'] = 'required|string|max:100';
        }

        $this->validate($rules);

        $paymentDate = isset($this->data['payment_date'])
            ? Carbon::parse($this->data['payment_date'])->format('Y-m-d H:i:s')
            : now()->format('Y-m-d H:i:s');

        $payment = floatval($this->data['payment'] ?? 0);

        $orders = Order::where('order_id', $this->order_id)->get();

        foreach ($orders as $order) {
            $subcategoryId = $order->subcategory_id;

            $currentTotal = $this->getLatestBalance($this->order_id, $subcategoryId, $order->total);
            $paymentAmount = min($currentTotal, $payment);
            $newBalance = max($currentTotal - $paymentAmount, 0);

            $count = payment::where('order_id', $this->order_id)
                ->where('subcategory_id', $subcategoryId)
                ->count() + 1;

            $payment_id = 'PYMT-' . $this->order_id . '-' . $subcategoryId . '-' . str_pad($count, 2, '0', STR_PAD_LEFT);

            // Base fields
            $paymentData = [
                'payment_id' => $payment_id,
                'order_id' => $this->order_id,
                'subcategory_id' => $subcategoryId,
                'jo_number' => $order->jo_number,
                'name' => $order->name,
                'address' => $order->address,
                'amount' => $order->amount,
                'total' => $currentTotal,
                'balance' => $newBalance,
                'payment' => $paymentAmount,
                'payment_method' => $this->data['payment_method'],
                'reference_number' => $this->data['reference_number'] ?? null,
                'payment_date' => $paymentDate,
                'payment_status' => $this->data['payment_status'] ?? null,
                'remarks' => $this->data['remarks'] ?? '',
                'status' => $this->data['status'] ?? '',
                'isActive' => true,
                'service_by' => Auth::user()->username,
            ];

            // Method-specific fields
            switch ($this->data['payment_method']) {
                case 'GCash':
                    $paymentData['gcash_number'] = $this->data['gcash_number'] ?? null;
                    $paymentData['gcash_account_name'] = $this->data['gcash_account_name'] ?? null;
                    break;

                case 'Bank Transfer':
                    $paymentData['bank_name'] = $this->data['bank_name'] ?? null;
                    break;

                case 'Cheque':
                    $paymentData['cheque_number'] = $this->data['cheque_number'] ?? null;
                    $paymentData['cheque_date'] = $this->data['cheque_date'] ?? null;
                    $paymentData['bank_name'] = $this->data['bank_name'] ?? null;
                    break;

                case 'Cash':
                    // Optional: Include `cash_received_by` if you want
                    $paymentData['cash_received_by'] = Auth::user()->username;
                    break;
            }

            payment::create($paymentData);
        }

        Order::where('order_id', $this->order_id)->update([
            'status' => $this->data['status'] ?? 'Paid',
            'remarks' => $this->data['remarks'] ?? '',
        ]);

        $this->emit('refreshComponent');
        $this->reset(['data', 'order_id']);
        $this->isOpen = false;
        $this->step = 1;

        session()->flash('messageInsert', 'Order is successfully paid!');
    } catch (\Exception $e) {
        session()->flash('errorInsert', 'Failed to save order: ' . $e->getMessage());
    }
}
}
