<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Pricelist;
use App\Models\Order;
use App\Models\RequestReceipt;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class OrderreceiptRequestModal extends Component
{
    public $Category = [];
    public $SubCategory = [];
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

    public function render()
    {
        return view('livewire.orderreceipt-request-modal');
    }

   public function mount()
{
    $this->Category = Category::where('isActive', 1)->get();
    $this->SubCategory = SubCategory::where('isActive', 1)->get();

    // Get all order_ids with existing receipts (combination of order_id + category_id + subcategory_id)
    $existingReceipts = RequestReceipt::select('order_id', 'category_id', 'subcategory_id')->get();

    // Filter out orders that already have a matching receipt
    $this->Order = Order::where('isActive', 1)->get()->filter(function ($order) use ($existingReceipts) {
        foreach ($existingReceipts as $receipt) {
            if (
                $order->order_id == $receipt->order_id &&
                $order->category_id == $receipt->category_id &&
                $order->subcategory_id == $receipt->subcategory_id
            ) {
                return false; // Exclude this order
            }
        }
        return true; // Keep this order
    })->values(); // Reset index
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

    public function getSelectedOrderName()
    {
        $order = Order::where('order_id', $this->order_id)->first();
        return $order ? $order->name : '';
    }

   public function updatedOrderId($value)
{
    // Load related subcategories
    $subcatIds = Order::where('order_id', $value)->pluck('subcategory_id')->unique();
    $this->filteredSubCategories = SubCategory::whereIn('subcategory_id', $subcatIds)->get();
    $this->selected_subcategory_id = null;

    // Get latest payment details for selected order
    $latestPayment = Payment::where('order_id', $value)
        ->where('isActive', 1) // Optional: Only if you're using soft deletes/flags
        ->latest('id')
        ->first();

    if ($latestPayment) {
        $this->data['payment'] = $latestPayment->payment;
        $this->data['balance'] = $latestPayment->balance;
        $this->data['total'] = $latestPayment->total;
        $this->data['reference_number'] = $latestPayment->reference_number;
        $this->data['payment_method'] = $latestPayment->payment_method;
        $this->data['payment_date'] = $latestPayment->payment_date
            ? \Carbon\Carbon::parse($latestPayment->payment_date)->format('Y-m-d')
            : '';
        $this->data['payment_status'] = $latestPayment->payment_status;
        $this->data['remarks'] = $latestPayment->remarks;
        $this->data['status'] = $latestPayment->status;
    } else {
        // Reset if no payment
        $this->data['payment'] = 0;
        $this->data['balance'] = 0;
        $this->data['total'] = 0;
        $this->data['reference_number'] = '';
        $this->data['payment_method'] = '';
        $this->data['payment_date'] = '';
        $this->data['payment_status'] = '';
        $this->data['remarks'] = '';
        $this->data['status'] = '';
    }
}


    public function updatedSelectedSubcategoryId($value)
    {
        // Fetch the order row for the selected order_id and subcategory_id
        $order = Order::where('order_id', $this->order_id)
            ->where('subcategory_id', $value)
            ->first();

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
            $this->data['amount'] = $order->amount;
            $this->data['price'] = $order->price;
            $this->data['status'] = $order->status;
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

   
       public function updatedSelectedpayment($value)
{
    if ($value === 'Partial') {
        // Get latest payment balance for the order
        $latestPayment = Payment::where('order_id', $this->order_id)
            ->where('isActive', 1)
            ->latest('id')
            ->first();

        $this->data['balance'] = $latestPayment ? $latestPayment->balance : 0;
        $this->data['payment_status'] = 'Partial';
    } elseif ($value === 'Paid') {
        $this->data['payment_status'] = 'Paid';
        $this->data['balance'] = 0;
    } elseif ($value === 'Unpaid') {
        $this->data['payment_status'] = 'Unpaid';
        $this->data['balance'] = 0;
    } else {
        $this->data['payment_status'] = null;
        $this->data['balance'] = 0;
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
                'status' => $this->data['status'],
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
            session()->flash('errorInsert', 'Error creating order receipt: ' . $e->getMessage());
        }   
    }
}