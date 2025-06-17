<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Livewire\WithPagination;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Pricelist;
use App\Models\Order;
use App\Models\payment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderTable extends Component implements HasTable
{
    use WithPagination, InteractsWithTable {
        WithPagination::resetPage insteadof InteractsWithTable;
    }

    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
    public function previousStep1() { $this->step = 2; }

    protected $listeners = ['refreshComponent' => '$refresh'];

    public $search = '';
    public $isEditModalOpen = false;
    public $step = 1;
    public $filterActivation = '';
    public $Category = [];
    public $SubCategory = [];

    public $editOrders = [
        'id' => null,
        'qty' => null,
        'price' => null,
        'amount' => null,
        'deadline' => null,
        'status' => null,
        'remarks' => null,
        'total' => null,
        'isActive' => null,
    ];

    public $payment;
    public $order;

    // public function mount($orderId = null)
    // {
    //     // Always load categories and subcategories
    //     $this->Category = Category::pluck('category_name', 'category_id')->toArray();
    //     $this->SubCategory = SubCategory::pluck('subcategory_name', 'subcategory_id')->toArray();

    //     // Load order with payment relationship
    //     if ($orderId) {
    //        $this->order = Order::with('payment')->where('order_id', $orderId)->first();
    //     }
    // }


     public function mount()
    {
        // Load categories and subcategories
        $this->Category = Category::pluck('category_name', 'category_id')->toArray();
        $this->SubCategory = SubCategory::pluck('subcategory_name', 'subcategory_id')->toArray();

        // Load all orders with payment relation
        $this->orders = Order::with('payment')->get();
    }


    public function rules()
    {
        return [
            'editOrders.qty' => 'required|numeric|min:1',
            'editOrders.price' => 'required|numeric',
            'editOrders.amount' => 'required|numeric',
             'editOrders.total' => 'required|numeric',
            'editOrders.deadline' => 'required|date',
            'editOrders.status' => 'required|string|max:255',
            'editOrders.remarks' => 'required|string|max:255',
            'editOrders.isActive' => 'required|boolean',
        ];
    }

    public function updated($field)
    {
        $category_id = $this->editOrders['category_id'] ?? null;
        $subcategory_id = $this->editOrders['subcategory_id'] ?? null;
        $qty = (float)($this->editOrders['qty'] ?? 0);

        if (in_array($field, ['editOrders.category_id', 'editOrders.subcategory_id', 'editOrders.qty'])) {
            if ($category_id && $subcategory_id && $qty) {
                $price = $this->getPriceFromPricelist($category_id, $subcategory_id, $qty);
                $amount = $price * $qty;

                $this->editOrders['price'] = $price;
                $this->editOrders['amount'] = $amount;
                $this->editOrders['total'] = $amount;
                $this->editOrders['balance'] = $amount;
            }
        }

        if (in_array($field, ['editOrders.amount', 'editOrders.payment'])) {
            $amount = (float)($this->editOrders['amount'] ?? 0);
            $payment = (float)($this->editOrders['payment'] ?? 0);
            $balance = max($amount - $payment, 0);

            $this->editOrders['total'] = $amount;
            $this->editOrders['balance'] = $balance;
        }
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

    public function update()
    {
        try{
        $this->validate(
            [
            'editOrders.qty' => 'required|numeric|min:1',
            'editOrders.price' => 'required|numeric',
            'editOrders.amount' => 'required|numeric',
            'editOrders.total' => 'required|numeric',
            'editOrders.deadline' => 'required|date',
            'editOrders.status' => 'required|string|max:255',
            'editOrders.remarks' => 'required|string|max:255',
            'editOrders.isActive' => 'required|boolean', 
            ]
        );

        $order = Order::find($this->editOrders['id']);

        if ($order) {
            $order->update([
                'qty' => $this->editOrders['qty'],
                'price' => $this->editOrders['price'],
                'amount' => $this->editOrders['amount'],
                'total' => $this->editOrders['total'],
                'deadline' => $this->editOrders['deadline'],
                'status' => $this->editOrders['status'],
                'remarks' => $this->editOrders['remarks'],
                'isActive' => $this->editOrders['isActive'],
                'updated_by' => Auth::user()->username,
            ]);

            // // 🔄 Update the related payment record (if any)
            // $payment = \App\Models\payment::where('order_id', $order->order_id)
            //     ->where('subcategory_id', $order->subcategory_id)
            //     ->latest()
            //     ->first();

            // if ($payment) {
            //     $payment->update([
            //         'total' => $this->editOrders['total'],
            //         'balance' => $this->editOrders['balance'],
            //         'amount' => $this->editOrders['amount'],
            //         'status' => $this->editOrders['status'] ?? $order->status,
            //         'remarks' => $this->editOrders['remarks'] ?? $order->remarks,
            //     ]);
            // }

           

            $this->emit('refreshTable');
            $this->isEditModalOpen = false;
            $this->dispatchBrowserEvent('closeEditModal');
            session()->flash('messageUpdate', 'Order updated successfully.');
        } 
    }catch (\Exception $e) {
            session()->flash('errorUpdate', 'Failed to save order: ' . $e->getMessage());
        }
    }

    
    public function closeModal()
    {
        $this->isOpen = false;
        $this->step = 1;


    }


    
//for the date filter
    public $filterDate;
    public $dateRange;
    public $date_from;
    public $date_to;
    

    public function edit($id)
    {
        $order = Order::find($id);

        if ($order) {
            $this->editOrders = [
                'id' => $order->id,
                'category_id' => $order->category_id,
                'subcategory_id' => $order->subcategory_id,
                'qty' => $order->qty,
                'price' => $order->price,
                'amount' => $order->amount,
                'payment' => $order->payment,
                'balance' => $order->balance,
                'total' => $order->total,
                'deadline' => $order->deadline,
                'status' => $order->status,
                'remarks' => $order->remarks,
                'isActive' => $order->isActive,
            ];

            $this->isEditModalOpen = true;
        } else {
            session()->flash('error', 'Order not found.');
        }
    }

    public function cancelEdit()
    {
        $this->reset('editOrders');
        $this->isEditModalOpen = false;
    }

    public function getTableQuery()
    {
        return Order::where('isActive', 1);
    }

    public function getFilteredRecords()
    {
        return Order::query()
            ->when($this->filterActivation !== '', fn($query) => $query->where('isActive', $this->filterActivation))
            ->when($this->search !== '', function ($query) {
                return $query->where(function ($q) {
                    $q->where('order_id', 'like', "%{$this->search}%")
                      ->orwhere('name', 'like', "%{$this->search}%");
                });
            })->get();
    }

    public function getRowCountProperty()
    {
        return $this->getFilteredRecords()->count();
    }

    public function render()
    {
        $query = Order::query();

        if ($this->filterActivation !== '') {
            $query->where('isActive', $this->filterActivation);
        } else {
            $query->where('isActive', 1);
        }

         if ($this->date_from) {
        $query->where(function ($q) {
            $q->whereDate('deadline', '>=', $this->date_from);
        });
    }
    if ($this->date_to) {
        $query->where(function ($q) {
            $q->whereDate('deadline', '<=', $this->date_to);
        });
    }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                 $q->where('order_id', 'like', "%{$this->search}%")
                      ->orwhere('name', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.order-table', [
            'records' => $query->get(),
            'rowCount' => $query->count(),
        ]);
    }
}
