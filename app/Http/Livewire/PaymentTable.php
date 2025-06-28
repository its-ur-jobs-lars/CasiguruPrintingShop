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

class PaymentTable extends Component implements HasTable
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
    public $paymentMethodFilter = '';

    public $editOrders = [
        'id' => null,
        'qty' => null,
        'price' => null,
        'amount' => null,
        'payment' => null,
        'balance' => null,
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
        // $this->Category = Category::pluck('category_name', 'category_id')->toArray();
        $this->SubCategory = SubCategory::pluck('subcategory_name', 'subcategory_id')->toArray();

        
    }


    public function rules()
    {
        return [
            'editOrders.qty' => 'required|numeric|min:1',
            'editOrders.price' => 'required|numeric',
            'editOrders.amount' => 'required|numeric',
            'editOrders.payment' => 'required|numeric|min:0',
            'editOrders.balance' => 'required|numeric',
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
        $this->validate(
            [
            'editOrders.amount' => 'required|numeric',
            'editOrders.payment' => 'required|numeric|min:0',
            'editOrders.balance' => 'required|numeric',
            'editOrders.total' => 'required|numeric',
            'editOrders.deadline' => 'required|date',
            'editOrders.status' => 'required|string|max:255',
            'editOrders.remarks' => 'required|string|max:255',
            'editOrders.isActive' => 'required|boolean', 
            ]
        );

        $order = payment::find($this->editOrders['id']);

        if ($order) {
            $order->update([
                'amount' => $this->editOrders['amount'],
                'payment' => $this->editOrders['payment'],
                'total' => $this->editOrders['total'],
                'balance' => $this->editOrders['balance'],
                'deadline' => $this->editOrders['deadline'],
                'status' => $this->editOrders['status'],
                'remarks' => $this->editOrders['remarks'],
                'isActive' => $this->editOrders['isActive'],
                'updated_by' => Auth::user()->username,
            ]);

            $this->emit('refreshTable');
            $this->isEditModalOpen = false;
            $this->dispatchBrowserEvent('closeEditModal');
            session()->flash('messageUpdate', 'Order updated successfully.');
        } else {
            session()->flash('error', 'Order not found.');
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
    
    public $showThreadPayment = [];
    public $showPaymentThread = false;
    public $threadOrderId;

    public function edit($id)
    {
        $order = payment::find($id);

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
        return payment::where('isActive', 1);
    }


public function showThread($id)
{
    $payment = payment::find($id);

    if (!$payment) {
        $this->showThreadPayment = [];
        $this->showPaymentThread = false;
        $this->threadOrderId = null;
        return;
    }

    $order_id = $payment->order_id;

    // Get all payments for the same order
    $payments = payment::where('order_id', $order_id)
        ->orderBy('payment_date', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    // Group by exact payment date for display grouping
    $groupedPayments = $payments->groupBy(function ($item) {
        return Carbon::parse($item->payment_date)->format('Y-m-d H:i:s');
    });

    $result = [];
    $cumulativePaid = [];

    // Track payment count per subcategory (or per order if you want)
    $paymentCounters = [];

    foreach ($groupedPayments as $date => $group) {
        $paymentGroup = [];

        foreach ($group as $pay) {
            $order = Order::where('order_id', $pay->order_id)
                ->where('subcategory_id', $pay->subcategory_id)
                ->with('subcategory')
                ->first();

            $subcategoryId = $pay->subcategory_id;
            $orderTotal = $order?->total ?? 0;

            // Increment payment count for subcategory under this order
            $paymentCounters[$subcategoryId] = ($paymentCounters[$subcategoryId] ?? 0) + 1;
            $paymentLabel = 'Payment ' . $paymentCounters[$subcategoryId];

            // Accumulate payment total per subcategory
            $cumulativePaid[$subcategoryId] = ($cumulativePaid[$subcategoryId] ?? 0) + $pay->payment;

            // Calculate balance
            $balance = max($orderTotal - $cumulativePaid[$subcategoryId], 0);

            $paymentGroup[] = [
                'label' => $paymentLabel,
                'name' => $pay->name,
                'payment' => $pay->payment,
                'balance' => $balance,
                'total' => $orderTotal,
                'payment_date' => $pay->payment_date,
                'order_id' => $pay->order_id,
                'subcategory_name' => $order?->subcategory?->subcategory_name ?? 'No Subcategory',
                 'payment_method' => $pay->payment_method ?? '',
            ];
        }

        $result[] = [
            'date' => $date,
            'items' => $paymentGroup,
        ];
    }

    $this->showThreadPayment = $result;
    $this->showPaymentThread = true;
    $this->threadOrderId = $order_id;

}


    public function getFilteredRecords()
{
    return payment::query()
        ->when($this->filterActivation !== '', fn($query) =>
            $query->where('isActive', $this->filterActivation)
        )

        ->when($this->search !== '', function ($query) {
            $query->where(function ($q) {
                $q->where('order_id', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%")
                    ->orWhere('payment_id', 'like', "%{$this->search}%");
            });
        })

        ->when($this->paymentMethodFilter !== '', fn($query) =>
            $query->where('payment_method', $this->paymentMethodFilter)
        )

        ->when($this->date_from, fn($query) =>
            $query->whereDate('payment_date', '>=', $this->date_from)
        )

        ->when($this->date_to, fn($query) =>
            $query->whereDate('payment_date', '<=', $this->date_to)
        )

        ->get();
}


    public function getRowCountProperty()
    {
        return $this->getFilteredRecords()->count();
    }

    public function render()
    {
        $query = payment::query();

        if ($this->filterActivation !== '') {
            $query->where('isActive', $this->filterActivation);
        } else {
            $query->where('isActive', 1);
        }

         if ($this->date_from) {
        $query->where(function ($q) {
            $q->whereDate('payment_date', '>=', $this->date_from);
        });
    }
    if ($this->date_to) {
        $query->where(function ($q) {
            $q->whereDate('payment_date', '<=', $this->date_to);
        });
    }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                 $q->where('order_id', 'like', "%{$this->search}%")
                      ->orwhere('payment_id', 'like', "%{$this->search}%")
                       ->orwhere('name', 'like', "%{$this->search}%");
            });
        }

        
    // Apply payment method filter
    if ($this->paymentMethodFilter !== '') {
        $query->where('payment_method', $this->paymentMethodFilter);
    }

        return view('livewire.payment-table', [
            'records' => $query->get(),
            'rowCount' => $query->count(),
        ]);
    }
}

