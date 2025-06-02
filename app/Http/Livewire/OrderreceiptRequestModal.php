<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Pricelist;
use App\Models\Order;
use App\Models\RequestReceipt;
use Illuminate\Support\Facades\Auth;


class OrderreceiptRequestModal extends Component
{
    public function render()
    {
        return view('livewire.orderreceipt-request-modal');
    }

     public $Category = [];
    public $SubCategory = [];
    public $Order = [];

    public function mount()
    {
         $this->Order = Order::where('isActive', 1)->get();
        $this->Category = Category::where('isActive', 1)->get();
        $this->SubCategory = SubCategory::where('isActive', 1)->get();
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
    public $isOpen = false;
    public $data = [];
    public $step = 1;

    public $order_id;

public function updatedOrderId($value)
{
    $order = Order::where('order_id', $value)->first();
    if ($order) {
        $this->data['date'] = $order->date;
        $this->data['name'] = $order->name;
        $this->data['contact_no'] = $order->contact_no;
        $this->data['address'] = $order->address;
        $this->data['jo_number'] = $order->jo_number;
        $this->data['category_id'] = $order->category_id;
        $this->data['subcategory_id'] = $order->subcategory_id;
        $this->data['qty'] = $order->qty;
        $this->data['total'] = $order->total;
        $this->data['payment'] = $order->payment;
        $this->data['balance'] = $order->balance;
        $this->data['amount'] = $order->amount;
        $this->data['price'] = $order->price;
    }
}


private function getPriceFromPricelist($category_id, $subcategory_id, $qty)
    {
        $pricelist = Pricelist::where('category_id', $category_id)
            ->where('subcategory_id', $subcategory_id)
            ->first();

        if (!$pricelist) {
            return 0;
        }

        if ($qty >= 10 && $qty <= 50) {
            return $pricelist->price_10_50;
        } elseif ($qty >= 51 && $qty <= 100) {
            return $pricelist->price_51_100;
        } elseif ($qty >= 101 && $qty <= 500) {
            return $pricelist->price_101_500;
        } else {
            return $pricelist->price_10_50;
        }
    }




    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
    public function nextStep2() { $this->step = 4; }
    public function previousStep1() { $this->step = 2; }

public $selectedpayment;

public function updatedSelectedpayment($value)
{
    if ($value == 'Paid') {
        $this->data['payment_status'] = 'Paid';
    } elseif ($value == 'Balance') {
        $this->data['payment_status'] = 'Balance';
    } else {
        $this->data['payment_status'] = null;
    }
}

public function save()
{
    try {
        $this->validate([
            'data.name' => 'required',
            'data.contact_no' => 'required',
            'data.address' => 'nullable|string|max:255',
            'data.category_id' => 'required',
            'data.subcategory_id' => 'required',
            'data.qty' => 'required|numeric',
            'data.price' => 'required|numeric',
            'data.amount' => 'required|numeric',
            'data.total' => 'required|numeric',
            'data.payment' => 'required|numeric',
            'data.balance' => 'required|numeric',
            'data.jo_number' => 'nullable|string|max:255',
            'data.payment_method' => 'nullable|string|max:255',
            'data.reference_number' => 'nullable|string|max:255',
            'data.payment_date' => 'nullable|date_format:Y-m-d',
            'data.status' => 'nullable|string|max:50',
            'data.payment_status' => 'nullable|string|max:50',
            'data.remarks' => 'nullable|string|max:500',
        ]);

        // Generate unique order_receipt_id like OR-0000236
        $lastReceipt = RequestReceipt::latest('id')->first();
        $nextId = $lastReceipt ? $lastReceipt->id + 1 : 1;
        $order_receipt_id = 'OR-' . str_pad($nextId, 7, '0', STR_PAD_LEFT);

        // Use provided payment_date or default to today
        $paymentDate = isset($this->data['payment_date']) ? $this->data['payment_date'] : now()->toDateString();

        // Use the selected order_id from the component property
        $order_id = $this->order_id;

        RequestReceipt::create([
            'order_receipt_id' => $order_receipt_id,
            'order_id' => $order_id,
            'jo_number' => $this->data['jo_number'] ?? null,
            'date' => now()->toDateString(),

            // Customer info
            'name' => $this->data['name'],
            'contact_no' => $this->data['contact_no'],
            'address' => $this->data['address'] ?? '',

            // Financials
            'category_id' => $this->data['category_id'],
            'subcategory_id' => $this->data['subcategory_id'],
            'qty' => $this->data['qty'],
            'price' => $this->data['price'],
            'amount' => $this->data['amount'],
            'total' => $this->data['total'],
            'payment' => $this->data['payment'],
            'balance' => $this->data['balance'],

            // Payment details
            'payment_method' => $this->data['payment_method'] ?? null,
            'reference_number' => $this->data['reference_number'] ?? null,
            'payment_date' => $paymentDate,
            'status' => $this->data['status'] ?? 'Active',
            'payment_status' => $this->data['payment_status'] ?? null,
            'remarks' => $this->data['remarks'] ?? '',

            // Signatures and flags
            'is_conforme_signed' => true,
            'is_received_signed' => true,
            'isActive' => true,

            // User info
            'service_by' => Auth::user()->username,
        ]);

        $this->emit('refreshComponent');
        $this->reset(['data', 'order_id']);
        $this->isOpen = false;
        $this->step = 1;

        session()->flash('messageInsert', 'Order receipt successfully created!');
    } catch (\Exception $e) {
    dd($e->getMessage()); // <-- TEMPORARY for debugging
    session()->flash('errorInsert', 'Failed to save order: ' . $e->getMessage());
}
    }
}