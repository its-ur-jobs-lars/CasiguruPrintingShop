<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Pricelist;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class AddOrder extends Component
{
    public $data = [];
    public $Category = [];
    public $SubCategory = [];
    public bool $isOpen = false;
    public bool $isSuccess = false;
    public $step = 1;

    public function render()
    {
        return view('livewire.add-order');
    }

    public function mount()
    {
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

    public $selectedpayment;
    public function Selectedpayment(){
        if ($this->Selectedpayment == 'Downpayment') {
            $this->data['payment'] = '';
        } elseif ($this->Selectedpayment == 'Full Payment') {
            $this->data['payment'] = ''; // Clear the serial number for input
        }
}

    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
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

    public function updated($field)
{
    $category_id = $this->data['category_id'] ?? null;
    $subcategory_id = $this->data['subcategory_id'] ?? null;
    $qty = (float)($this->data['qty'] ?? 0);

    if (in_array($field, ['data.category_id', 'data.subcategory_id', 'data.qty'])) {
        if ($category_id && $subcategory_id && $qty) {
            $price = $this->getPriceFromPricelist($category_id, $subcategory_id, $qty);
            $amount = $price * $qty;

            $this->data['price'] = $price;
            $this->data['amount'] = $amount;
            $this->data['total'] = $amount;
            $this->data['balance'] = $amount;
        }
    }

    if (in_array($field, ['data.amount', 'data.payment'])) {
        $amount = (float)($this->data['amount'] ?? 0);
        $payment = (float)($this->data['payment'] ?? 0);
        $balance = max($amount - $payment, 0);

        $this->data['total'] = $amount;
        $this->data['balance'] = $balance;
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
                'data.deadline' => 'nullable|date',
                'data.status' => 'nullable|string|max:50',
                'data.remarks' => 'nullable|string|max:500',
            ]);

            $deadline = isset($this->data['deadline']) ? date('Ymd', strtotime($this->data['deadline'])) : date('Ymd');

            $lastOrder = Order::where('order_id', 'like', 'ORD-%')->orderByDesc('order_id')->first();
            $nextSequence = ($lastOrder && preg_match('/ORD-(\d{4})-/', $lastOrder->order_id, $matches)) ? intval($matches[1]) + 1 : 1;
            $sequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            $order_id = "ORD-{$sequence}-{$deadline}";

            Order::create([
                'order_id' => $order_id,
                'name' => $this->data['name'],
                'contact_no' => $this->data['contact_no'],
                'address' => $this->data['address'] ?? '',
                'category_id' => $this->data['category_id'],
                'subcategory_id' => $this->data['subcategory_id'],
                'qty' => $this->data['qty'],
                'price' => $this->data['price'],
                'amount' => $this->data['amount'],
                'payment' => $this->data['payment'],
                'total' => $this->data['total'],
                'balance' => $this->data['balance'],
                'jo_number' => $this->data['jo_number'] ?? '',
                'deadline' => $this->data['deadline'] ?? null,
                'status' => $this->data['status'] ?? '',
                'remarks' => $this->data['remarks'] ?? '',
                'added_by' => Auth::user()->username,
            ]);

            $this->emit('refreshComponent');
            $this->reset(['data']);
            $this->isOpen = false;
            $this->step = 1;
            session()->flash('messageInsert', 'Order successfully created!');
        } catch (\Exception $e) {
            session()->flash('errorInsert', 'Failed to save order: ' . $e->getMessage());
        }
    }
}
