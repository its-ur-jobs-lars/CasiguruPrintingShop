<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Pricelist;
use App\Models\Order;
use App\Models\RequestReceipt;
use App\Models\payment;
use Illuminate\Support\Facades\Auth;

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

    public function mount()
    {
        $this->Order = Order::where('isActive', 1)->get();
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
        $subcatIds = Order::where('order_id', $value)->pluck('subcategory_id')->unique();
        $this->filteredSubCategories = SubCategory::whereIn('subcategory_id', $subcatIds)->get();
        $this->selected_subcategory_id = null;
    }

    public function updatedSelectedSubcategoryId($value)
    {
        $order = Order::where('order_id', $this->order_id)
            ->where('subcategory_id', $value)
            ->first();

        if ($order) {
            $this->data['date'] = $order->deadline;
            $this->data['name'] = $order->name;
            $this->data['contact_no'] = $order->contact_no;
            $this->data['address'] = $order->address;
            $this->data['jo_number'] = $order->jo_number;
            $this->data['total'] = $order->total;
            $this->data['balance'] = $order->balance;
            $this->data['amount'] = $order->amount;
             $this->data['status'] = $order->status;
             $this->data['qty'] = $order->qty;
             $this->data['price'] = $order->price;
        }
    }

    // public function updatedSelectedpayment($value)
    // {
    //     if ($value === 'Partial') {
    //         $order = Order::where('order_id', $this->order_id)->first();
    //         $this->data['balance'] = $order ? $order->balance : 0;
    //         $this->data['payment_status'] = 'Partial';
    //     } elseif ($value === 'Paid') {
    //         $this->data['payment_status'] = 'Paid';
    //         $this->data['balance'] = 0;
    //     } elseif ($value === 'Unpaid') {
    //         $this->data['payment_status'] = 'Unpaid';
    //         $this->data['balance'] = 0;
    //     } else {
    //         $this->data['payment_status'] = null;
    //         $this->data['balance'] = 0;
    //     }
    // }

    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
    public function nextStep2() { $this->step = 3; }
    public function previousStep1() { $this->step = 2; }

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

        private function recalculateTotal()
        {
            $price = $this->data['price'] ?? 0;
            $qty = $this->data['qty'] ?? 0;
            $this->data['total'] = $price * $qty;

            // Logic based on selected payment type
            if ($this->selectedpayment === 'Full Payment') {
                $this->data['payment'] = $this->data['total'];
                $this->data['balance'] = 0;
            } elseif ($this->selectedpayment === 'Downpayment') {
                $payment = $this->data['payment'] ?? 0;
                $this->data['balance'] = $this->data['total'] - $payment;
            } else {
                $this->data['payment'] = 0;
                $this->data['balance'] = $this->data['total'];
            }
        }


        public function updatedSelectedpayment($value)
{
    if ($value === 'Full Payment') {
        $this->recalculateTotal(); // Ensure total is accurate
       
    }

    if ($value === 'Downpayment') {
        $this->data['payment'] = ''; // Reset for manual input
    }
}


        public function updated($property)
{
    if (
        in_array($property, [
            'data.price',
            'data.qty',
            'data.payment',
            'selectedpayment'
        ])
    ) {
        $this->recalculateTotal();
    }
}




    public function updatedData($value, $key)
    {
        if ($key === 'payment') {
            $this->recalculateTotal();
        }
    }

    public function Selectedpayment()
    {
        $this->data['payment'] = '';
    }

    public function save()
    {
        try {
            $this->validate([
                'data.name' => 'required',
                'data.address' => 'nullable|string|max:255',
                'data.amount' => 'required|numeric',
                'data.total' => 'required|numeric',
                'data.balance' => 'required|numeric',
                'data.jo_number' => 'nullable|string|max:255',
                'data.payment_method' => 'nullable|string|max:255',
                'data.reference_number' => 'nullable|string|max:255',
                'data.payment_date' => 'nullable|date_format:Y-m-d',
                'data.status' => 'nullable|string|max:50',
                'data.payment_status' => 'nullable|string|max:50',
                'data.remarks' => 'nullable|string|max:500',
            ]);

            $count = payment::where('order_id', $this->order_id)->count() + 1;
           $payment_id = 'PYMT-' . $this->order_id . '-' . str_pad($count, 2, '0', STR_PAD_LEFT);
            $paymentDate = $this->data['payment_date'] ?? now()->toDateString();

            payment::create([
                'payment_id' => $payment_id,
                'order_id' => $this->order_id,
                'jo_number' => $this->data['jo_number'] ?? null,
               
                'name' => $this->data['name'],
                
                'address' => $this->data['address'] ?? null,
                'amount' => $this->data['amount'],
                'total' => $this->data['total'],
                'balance' => $this->data['balance'],
                'payment_method' => $this->data['payment_method'] ?? null,
                'reference_number' => $this->data['reference_number'] ?? null,
                'payment_date' => $paymentDate,
                'payment_status' => $this->data['payment_status'] ?? null,
                'remarks' => $this->data['remarks'] ?? '',
                'status' => $this->data['status'] ?? '',
                'isActive' => true,
                'service_by' => Auth::user()->username,
            ]);

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
