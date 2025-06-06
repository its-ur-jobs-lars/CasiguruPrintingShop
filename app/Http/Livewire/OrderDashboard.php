<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Pricelist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderDashboard extends Component
{
    public $showModal = false;
    public $data = [
        'name' => '',
        'contact_no' => '',
        'address' => '',
        'payment' => 0,
        'total' => 0,
        'balance' => 0,
        'jo_number' => '',
        'deadline' => '',
        'status' => '',
        'remarks' => '',
    ];

    public $items = [];
    public $orderItems = [];
    public $cart = [];

    public function mount()
    {
        $this->items = Pricelist::with(['category', 'subcategory'])->get();
    }

    public $showCart = false;


    public function closeModal()
    {
        $this->showModal = false;
        $this->showCart = true;
    }

    public function addToCart($id)
    {

         $this->showCart = true;

        $item = Pricelist::with(['category', 'subcategory'])->find($id);

        if (!$item) return;

        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']++;
            $qty = $this->cart[$id]['qty'];
            $this->cart[$id]['price'] = $this->getPriceFromPricelist($item->category_id, $item->subcategory_id, $qty);
        } else {
            $qty = 1;
            $this->cart[$id] = [
                'category_id' => $item->category_id,
                'subcategory_id' => $item->subcategory_id,
                'category_name' => $item->category->category_name ?? 'N/A',
                'subcategory_name' => $item->subcategory->subcategory_name ?? 'N/A',
                'price' => $this->getPriceFromPricelist($item->category_id, $item->subcategory_id, $qty),
                'qty' => $qty
            ];
        }
    }

    public function incrementQty($id)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']++;
            $this->updatePriceBasedOnQty($id);
        }
    }

    public function decrementQty($id)
    {
        if (isset($this->cart[$id]) && $this->cart[$id]['qty'] > 1) {
            $this->cart[$id]['qty']--;
            $this->updatePriceBasedOnQty($id);
        }
    }

    public function updatedCart($value, $key)
    {
        [$id, $field] = explode('.', $key);

        if ($field === 'qty' && isset($this->cart[$id])) {
            $qty = (int) $value;
            $item = Pricelist::with(['category', 'subcategory'])->find($id);

            if ($item) {
                $this->cart[$id]['qty'] = $qty;
                $this->cart[$id]['price'] = $this->getPriceFromPricelist(
                    $item->category_id,
                    $item->subcategory_id,
                    $qty
                );
            }
        }
    }

    private function updatePriceBasedOnQty($id)
    {
        if (!isset($this->cart[$id])) return;

        $item = $this->cart[$id];
        $price = $this->getPriceFromPricelist($item['category_id'], $item['subcategory_id'], $item['qty']);
        $this->cart[$id]['price'] = $price;
    }

    private function getPriceFromPricelist($category_id, $subcategory_id, $qty)
    {
        $pricelist = Pricelist::where('category_id', $category_id)
            ->where('subcategory_id', $subcategory_id)
            ->first();

        if (!$pricelist) return 0;

       if ($qty == 1) {
            return $pricelist->price_1;
        } elseif ($qty >= 2 && $qty <= 50) {
            return $pricelist->price_2_50;
        } elseif ($qty >= 51 && $qty <= 100) {
            return $pricelist->price_51_100;
        } elseif ($qty >= 101 && $qty <= 500) {
            return $pricelist->price_101_500;
        } elseif ($qty >= 501 && $qty <= 999) {
            return $pricelist->price_501_999;
        } elseif ($qty >= 1000) {
            return $pricelist->price_1000_up;
        } else {
            return $pricelist->price_1; // fallback for invalid or zero qty
        }

    }

    private function recalculateTotal()
    {
        $this->data['total'] = collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $this->data['balance'] = $this->data['total'] - $this->data['payment'];
    }

    public function confirmOrder()
    {
        session()->flash('message', 'Order confirmed!');
        $this->orderItems = [];
        $this->cart = [];
    }

    public function updatedData($value, $key)
{
    if ($key === 'payment') {
        $this->recalculateTotal();
    }
}


  public $selectedpayment;
    public function Selectedpayment(){
        if ($this->Selectedpayment == 'Downpayment') {
            $this->data['payment'] = '';
        } elseif ($this->Selectedpayment == 'Full Payment') {
            $this->data['payment'] = ''; // Clear the serial number for input
        }
}




    public function openModal()
    {
        $this->isOpen = true;
        $this->showModal = true;
        $this->showCart = false;
        $this->recalculateTotal();
    }



    public function save()
    {
        DB::beginTransaction();


        try {
            $deadline = isset($this->data['deadline']) ? date('Ymd', strtotime($this->data['deadline'])) : date('Ymd');
            $lastOrder = Order::where('order_id', 'like', 'ORD-%')->orderByDesc('order_id')->first();
            $nextSequence = ($lastOrder && preg_match('/ORD-(\d{4})-/', $lastOrder->order_id, $matches)) ? intval($matches[1]) + 1 : 1;
            $sequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            $order_id = "ORD-{$sequence}-{$deadline}";

            $latestJo = DB::table('orders')
                ->whereRaw("jo_number REGEXP '^[0-9]+$'") // only numeric strings
                ->orderByRaw("CAST(jo_number AS UNSIGNED) DESC")
                ->value('jo_number');

            $nextJoNumber = str_pad(((int)$latestJo) + 1, 5, '0', STR_PAD_LEFT);
            
            $this->recalculateTotal();

           foreach ($this->cart as $item) {
                Order::create([
                    'order_id' => $order_id,
                    'name' => $this->data['name'],
                    'contact_no' => $this->data['contact_no'],
                    'address' => $this->data['address'],
                    'category_id' => $item['category_id'],
                    'subcategory_id' => $item['subcategory_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'amount' => $item['qty'] * $item['price'],
                    'payment' => $this->data['payment'],
                    'total' => $this->data['total'],
                    'balance' => $this->data['balance'],
                    'jo_number' => $nextJoNumber,
                    'deadline' => $this->data['deadline'] ?? null,
                    'status' => $this->data['status'],
                    'remarks' => $this->data['remarks'] ?? '',
                    'added_by' => Auth::user()->username,
                ]);
            }

            DB::commit();

            $this->reset(['cart', 'data']);
            $this->showModal = false;
            session()->flash('messageInsert', 'Order successfully created!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('errorInsert', 'Failed to save order: ' . $e->getMessage());
        }
    }

//     public $activeCategory = null;

// public function showCategoryModal($categoryName)
// {
//     $this->activeCategory = $categoryName;
// }

    public function render()
    {
        return view('livewire.order-dashboard');
    }
}
