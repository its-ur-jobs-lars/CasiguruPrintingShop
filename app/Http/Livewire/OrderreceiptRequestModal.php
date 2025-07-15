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
    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
    public function nextStep2() { $this->step = 4; }
    public function previousStep1() { $this->step = 2; }

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
    public $isGovernment = false;

    public function render()
    {
        return view('livewire.orderreceipt-request-modal');
    }

    public function mount()
{
    $this->Category = Category::where('isActive', 1)->get();
    $this->SubCategory = SubCategory::where('isActive', 1)->get();

    // Fetch all issued receipt combinations
    $existingReceipts = RequestReceipt::select('order_id', 'subcategory_id')->get();

    $this->Order = Order::where('isActive', 1)->get()->filter(function ($order) use ($existingReceipts) {
        foreach ($existingReceipts as $receipt) {
            if (
                $order->order_id == $receipt->order_id &&
                $order->subcategory_id == $receipt->subcategory_id
            ) {
                return false; // Exclude if receipt already exists for this order_id + subcategory
            }
        }
        return true;
    })->values();
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
    // Get subcategory IDs from all orders matching the selected order_id
    $subcatIds = Order::where('order_id', $value)
        ->pluck('subcategory_id')
        ->unique();

    // Get subcategory IDs that already have a receipt for this order_id
    $issuedSubcatIds = RequestReceipt::where('order_id', $value)
        ->pluck('subcategory_id');

    // Filter out issued subcategory IDs
    $filteredIds = $subcatIds->diff($issuedSubcatIds);

    // Get subcategory details
    $this->filteredSubCategories = SubCategory::whereIn('subcategory_id', $filteredIds)->get();

    $this->selected_subcategory_id = null;
}


    public function updatedSelectedSubcategoryId($value)
    {
        $order = Order::where('order_id', $this->order_id)
            ->where('subcategory_id', $value)
            ->first();

        if ($order) {
            $isGovernment = strtolower($order->customer_type) === 'government';
            $this->isGovernment = $isGovernment;

            $this->data = [
                'date' => $order->date,
                'name' => $order->name,
                'contact_no' => $order->contact_no,
                'address' => $order->address,
                'jo_number' => $order->jo_number,
                'category_id' => $order->category_id,
                'subcategory_id' => $order->subcategory_id,
                'qty' => $order->qty,
                'amount' => $order->amount,
                'price' => $order->price,
                'status' => $order->status,
                'customer_type' => $order->customer_type,
                'is_government' => $isGovernment,
            ];

            if ($isGovernment) {
                $this->data['total'] = $order->total;
                $this->data['payment'] = 0;
                $this->data['balance'] = $order->total;
                $this->data['payment_status'] = 'Unpaid';
                $this->data['payment_method'] = 'None';
                $this->data['reference_number'] = 'None';
                $this->data['payment_date'] = null;
                $this->data['remarks'] = 'Government client — no payment yet';
            } else {
                $latestPayment = Payment::where('order_id', $this->order_id)
                    ->where('subcategory_id', $value)
                    ->where('isActive', 1)
                    ->latest('id')
                    ->first();

                $this->data['total'] = $order->total;
                $this->data['payment'] = $latestPayment->payment ?? 0;
                $this->data['balance'] = $latestPayment->balance ?? 0;
                $this->data['payment_status'] = $latestPayment->payment_status ?? '';
                $this->data['payment_method'] = $latestPayment->payment_method ?? '';
                $this->data['reference_number'] = $latestPayment->reference_number ?? '';
                $this->data['payment_date'] = $latestPayment?->payment_date
                    ? \Carbon\Carbon::parse($latestPayment->payment_date)->format('Y-m-d')
                    : null;
                $this->data['remarks'] = $latestPayment->remarks ?? '';
            }
        }
    }

    public function updatedSelectedpayment($value)
    {
        if ($value === 'Partial') {
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
            $isGovernment = strtolower($this->data['customer_type'] ?? '') === 'government';

            $rules = [
                'data.name' => 'required',
                'data.contact_no' => 'required',
                'data.address' => 'nullable|string|max:255',
                'data.category_id' => 'required',
                'data.subcategory_id' => 'required',
                'data.qty' => 'required|numeric',
                'data.price' => 'required|numeric',
                'data.amount' => 'required|numeric',
                'data.jo_number' => 'nullable|string|max:255',
                'data.status' => 'nullable|string|max:50',
                'data.receipt_number' => 'nullable|string|max:50',
                'data.remarks' => 'nullable|string|max:500',
            ];

            if (!$isGovernment) {
                $rules = array_merge($rules, [
                    'data.total' => 'required|numeric',
                    'data.payment' => 'required|numeric',
                    'data.balance' => 'required|numeric',
                    'data.payment_method' => 'nullable|string|max:255',
                    'data.reference_number' => 'nullable|string|max:255',
                    'data.payment_date' => 'nullable|date_format:Y-m-d',
                    'data.payment_status' => 'nullable|string|max:50',
                ]);
            }

            $this->validate($rules);

            $lastReceipt = RequestReceipt::latest('id')->first();
            $nextId = $lastReceipt ? $lastReceipt->id + 1 : 1;
            $order_receipt_id = 'OR-' . str_pad($nextId, 7, '0', STR_PAD_LEFT);
            $paymentDate = !$isGovernment && $this->data['payment_date'] ? $this->data['payment_date'] : null;

            RequestReceipt::create([
                'order_receipt_id' => $order_receipt_id,
                'order_id' => $this->order_id,
                'jo_number' => $this->data['jo_number'] ?? null,
                'date' => now()->toDateString(),

                'name' => $this->data['name'],
                'contact_no' => $this->data['contact_no'],
                'address' => $this->data['address'] ?? '',
                'customer_type' => $this->data['customer_type'] ?? '',

                'category_id' => $this->data['category_id'],
                'subcategory_id' => $this->data['subcategory_id'],
                'qty' => $this->data['qty'],
                'price' => $this->data['price'],
                'amount' => $this->data['amount'],
                'total' => $this->data['total'] ?? 0,
                'payment' => $isGovernment ? 0 : ($this->data['payment'] ?? 0),
                'balance' => $isGovernment ? 0 : ($this->data['balance'] ?? 0),
                'payment_method' => $isGovernment ? 'None' : ($this->data['payment_method'] ?? ''),
                'reference_number' => $isGovernment ? 'None' : ($this->data['reference_number'] ?? ''),
                'receipt_number' => $isGovernment ? 'None' : ($this->data['receipt_number'] ?? ''),
                'payment_date' => $paymentDate,
                'status' => $this->data['status'] ?? '',
                'payment_status' => $isGovernment ? 'Unpaid' : ($this->data['payment_status'] ?? ''),
                'remarks' => $isGovernment ? 'Government client — no payment required.' : ($this->data['remarks'] ?? ''),

                'is_conforme_signed' => true,
                'is_received_signed' => true,
                'isActive' => true,
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
