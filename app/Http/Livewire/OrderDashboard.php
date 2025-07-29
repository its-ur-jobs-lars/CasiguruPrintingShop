<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Pricelist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Inventory; 

class OrderDashboard extends Component
{
    public $showModal = false;
    public $showCart = false;
    public $showOrder = true;
    public $search = '';
    public $items = [];
    public $orderItems = [];
    public $cart = [];
    public $quantities = [];

    public $data = [
        'name' => '',
        'contact_no' => '',
        'address' => '',
        'total' => 0,
        'email',
        'jo_number' => '',
        'deadline' => '',
        'status' => '',
        'remarks' => '',
    ];

    public function mount()
    {
        $this->items = Pricelist::with(['category', 'subcategory'])->get();
    }

    public function closeModal()
    {
        $this->showOrder = true;
        $this->showModal = false;
    }

//    public function addToCart($id)
// {
//     $item = Pricelist::with(['category', 'subcategory'])->find($id);
//     if (!$item) {
//         session()->flash('error', 'Item not found.');
//         return;
//     }

//     $requestedQty = isset($this->cart[$id]['qty']) ? intval($this->cart[$id]['qty']) : 1;

//     if ($requestedQty < 1) {
//         session()->flash('error', 'Quantity must be at least 1.');
//         return;
//     }

//     // Check inventory for sufficient stock
//     $inventory = inventory::where('category_id', $item->category_id)
//         ->where('subcategory_id', $item->subcategory_id)
//         ->first();

//     if (!$inventory || $inventory->quantity < $requestedQty) {
//         session()->flash('error', 'Not enough stock available.');
//         return;
//     }

//     // Deduct from inventory
//     $inventory->quantity -= $requestedQty;
//     $inventory->save();

//     // Add to cart (or update existing entry)
//     if (isset($this->cart[$id])) {
//         $this->cart[$id]['qty'] += $requestedQty;
//         $this->cart[$id]['price'] = $this->getPriceFromPricelist(
//             $item->category_id,
//             $item->subcategory_id,
//             $this->cart[$id]['qty']
//         );
//     } else {
//         $this->cart[$id] = [
//             'category_id' => $item->category_id,
//             'subcategory_id' => $item->subcategory_id,
//             'category_name' => $item->category->category_name ?? 'N/A',
//             'subcategory_name' => $item->subcategory->subcategory_name ?? 'N/A',
//             'image' => $item->subcategory->image ?? null,
//             'price' => $this->getPriceFromPricelist($item->category_id, $item->subcategory_id, $requestedQty),
//             'qty' => $requestedQty,
//         ];
//     }

//     $this->showCart = true;
// }

public function updatedCart($value, $key)
{
    [$id, $field] = explode('.', $key);

    if ($field === 'qty') {
        $qty = intval($value);
        if ($qty < 1) {
            $this->cart[$id]['qty'] = 1;
            return;
        }
        // Recalculate price based on new quantity
        $item = Pricelist::find($id);
        $this->cart[$id]['price'] = $this->getPriceFromPricelist(
            $this->cart[$id]['category_id'],
            $this->cart[$id]['subcategory_id'],
            $qty
        );
    }
    $this->recalculateTotal();
}


// public function addToCart($id)
// {
//     $item = Pricelist::with(['category', 'subcategory'])->find($id);

//     if (!$item) {
//         session()->flash('error', 'Item not found.');
//         return;
//     }

//     $requestedQty = intval($this->quantities[$id] ?? 1);

//     if ($requestedQty < 1) {
//         session()->flash('error', 'Quantity must be at least 1.');
//         return;
//     }

//     foreach ($this->cart as $id => $item) {
//         $inventory = Inventory::where('category_id', $item['category_id'])
//             ->where('subcategory_id', $item['subcategory_id'])
//             ->first();

//         if ($inventory) {
//             if ($inventory->quantity < $item['qty']) {
//                 session()->flash('errorInsert', 'Not enough stock for ' . ($item['subcategory_name'] ?? 'Item'));
//                 return;
//             }
//             $inventory->quantity -= $item['qty'];
//             $inventory->save();
//         }
//     }

//     // Combine existing + new qty for correct pricing
//     $totalQty = $requestedQty;
//     if (isset($this->cart[$id])) {
//         $totalQty += $this->cart[$id]['qty'];
//     }

//     $price = $this->getPriceFromPricelist($item->category_id, $item->subcategory_id, $totalQty);

//     logger("ID: $id | Qty: $totalQty | Price: $price");

//     if (isset($this->cart[$id])) {
//         $this->cart[$id]['qty'] += $requestedQty;
//         $this->cart[$id]['price'] = $price;
//     } else {
//         $this->cart[$id] = [
//             'category_id' => $item->category_id,
//             'subcategory_id' => $item->subcategory_id,
//             'category_name' => $item->category->category_name ?? 'N/A',
//             'subcategory_name' => $item->subcategory->subcategory_name ?? 'N/A',
//             'image' => $item->subcategory->image ?? null,
//             'price' => $price,
//             'qty' => $requestedQty,
//         ];
//     }

//     unset($this->quantities[$id]);
//     $this->showCart = true;
//     $this->recalculateTotal();
// }

// public function addToCart($id)
// {
//     $item = Pricelist::with(['category', 'subcategory'])->find($id);

//     if (!$item) {
//         session()->flash('error', 'Item not found.');
//         return;
//     }

//     $requestedQty = intval($this->quantities[$id] ?? 1);
//     if ($requestedQty < 1) {
//         session()->flash('error', 'Quantity must be at least 1.');
//         return;
//     }

//     $inventory = Inventory::where('category_id', $item->category_id)
//         ->where('subcategory_id', $item->subcategory_id)
//         ->first();

//     if (!$inventory || $inventory->quantity < $requestedQty) {
//         session()->flash('errorInsert', 'Not enough stock for ' . ($item->subcategory->subcategory_name ?? 'Item'));
//         return;
//     }

//     // // Combine quantity for proper price tier
//     // $existingQty = $this->cart[$id]['qty'] ?? 0;
//     // $totalQty = $existingQty + $requestedQty;

//     $price = $this->getPriceFromPricelist($item->category_id, $item->subcategory_id, $totalQty);

//     $this->cart[$id] = [
//         'category_id' => $item->category_id,
//         'subcategory_id' => $item->subcategory_id,
//         'category_name' => $item->category->category_name ?? 'N/A',
//         'subcategory_name' => $item->subcategory->subcategory_name ?? 'N/A',
//         'image' => $item->subcategory->image ?? null,
//         'price' => $price,
//         'qty' => $totalQty,
//     ];

//     // Deduct only the newly added quantity
//     $inventory->quantity -= $requestedQty;
//     $inventory->save();

//     unset($this->quantities[$id]);
//     $this->showCart = true;
//     $this->recalculateTotal();
// }


public function addToCart($id)
{
    $item = Pricelist::with(['category', 'subcategory'])->find($id);

    if (!$item) {
        session()->flash('error', 'Item not found.');
        return;
    }

    $requestedQty = intval($this->quantities[$id] ?? 1);
    if ($requestedQty < 1) {
        session()->flash('error', 'Quantity must be at least 1.');
        return;
    }

    $inventory = Inventory::where('category_id', $item->category_id)
        ->where('subcategory_id', $item->subcategory_id)
        ->first();

    if (!$inventory || $inventory->quantity < $requestedQty) {
        session()->flash('errorInsert', 'Not enough stock for ' . ($item->subcategory->subcategory_name ?? 'Item'));
        return;
    }

    // Calculate total quantity (existing in cart + new request)
    $existingQty = $this->cart[$id]['qty'] ?? 0;
    $totalQty = $existingQty + $requestedQty;

    $price = $this->getPriceFromPricelist($item->category_id, $item->subcategory_id, $totalQty);

    $this->cart[$id] = [
        'category_id' => $item->category_id,
        'subcategory_id' => $item->subcategory_id,
        'category_name' => $item->category->category_name ?? 'N/A',
        'subcategory_name' => $item->subcategory->subcategory_name ?? 'N/A',
        'image' => $item->subcategory->image ?? null,
        'price' => $price,
        'qty' => $totalQty,
    ];

    // Do NOT deduct inventory here! Only deduct on save/checkout.

    unset($this->quantities[$id]);
    $this->showCart = true;
    $this->recalculateTotal();
}




    private function getPriceFromPricelist($category_id, $subcategory_id, $qty)
    {
        $pricelist = Pricelist::where('category_id', $category_id)
            ->where('subcategory_id', $subcategory_id)
            ->first();

        if (!$pricelist) return 0;

        return match (true) {
            $qty == 1 => $pricelist->price_1,
            $qty <= 50 => $pricelist->price_2_50,
            $qty <= 100 => $pricelist->price_51_100,
            $qty <= 500 => $pricelist->price_101_500,
            $qty <= 999 => $pricelist->price_501_999,
            $qty >= 1000 => $pricelist->price_1000_up,
            default => $pricelist->price_1
        };
    }

    private function recalculateTotal()
    {
        $this->data['total'] = collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->showCart = false;
        $this->showOrder = false;
        $this->recalculateTotal();
    }

  


    public function save()
{
    DB::beginTransaction();

    try {
        $deadline = isset($this->data['deadline']) ? date('Ymd', strtotime($this->data['deadline'])) : date('Ymd');
        $lastOrder = Order::where('order_id', 'like', 'ORD-%')->orderByDesc('order_id')->first();
        $nextSequence = ($lastOrder && preg_match('/ORD-(\d{4})-/', $lastOrder->order_id, $matches)) ? intval($matches[1]) + 1 : 1;
        $order_id = 'ORD-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT) . "-$deadline";

        $latestJo = DB::table('orders')
            ->whereRaw("jo_number REGEXP '^[0-9]+$'")
            ->orderByRaw("CAST(jo_number AS UNSIGNED) DESC")
            ->value('jo_number');

        $nextJoNumber = str_pad(((int)$latestJo) + 1, 5, '0', STR_PAD_LEFT);

        $this->recalculateTotal();

        $grandTotal = collect($this->cart)->sum(function ($item) {
            return ($item['qty'] * $item['price']) + (
                ($item['layout_option'] ?? '') === 'with_fee' ? ($item['layout_fee'] ?? 0) : 0
            );
        });

       foreach ($this->cart as $item) {
            // Deduct inventory per item
            $inventory = Inventory::where('category_id', $item['category_id'])
                ->where('subcategory_id', $item['subcategory_id'])
                ->first();

            if (!$inventory || $inventory->quantity < $item['qty']) {
                DB::rollBack();
                session()->flash('errorInsert', 'Not enough stock for ' . ($item['subcategory_name'] ?? 'Item'));
                return;
            }

            // $inventory->quantity -= $item['qty'];
            // $inventory->save();

            // Save order
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
                'layout_fee' => $item['layout_fee'] ?? 0,
                'email' =>  $this->data['email'],
                'total' => $grandTotal,
                'jo_number' => $nextJoNumber,
                'deadline' => $this->data['deadline'] ?? null,
                'customer_type' => $this->data['customer_type'],
                'status' => 'Pending Production', // Set as pending
                'remarks' => $this->data['remarks'] ?? '',
                'added_by' => Auth::user()->username,
            ]);
        }

        DB::commit();
        $this->reset(['cart', 'data']);
        $this->showModal = false;
        $this->showOrder = true;
        session()->flash('messageInsert', 'Order successfully created!');
    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('errorInsert', 'Failed to save order: ' . $e->getMessage());
    }
}


    public function render()
    {
        $items = Pricelist::with(['category', 'subcategory'])->get();

    // Filter items with inventory quantity > 0
    $items = $items->filter(function ($item) {
        $inventory = inventory::where('category_id', $item->category_id)
            ->where('subcategory_id', $item->subcategory_id)
            ->first();

        return $inventory && $inventory->quantity > 0;
    });

        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $items = $items->filter(function ($item) use ($search) {
                $category = strtolower($item->category->category_name ?? '');
                $subcategory = strtolower($item->subcategory->subcategory_name ?? '');
                return str_contains($category, $search) || str_contains($subcategory, $search);
            });
        }

        $groupedItems = $items->groupBy(fn($item) => $item->category->category_name ?? 'No Category');

        return view('livewire.order-dashboard', [
            'groupedItems' => $groupedItems,
        ]);
    }
}
