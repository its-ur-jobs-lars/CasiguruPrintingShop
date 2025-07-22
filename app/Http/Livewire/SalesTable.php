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
use Illuminate\Support\Facades\DB;
use App\Models\inventory;
use App\Models\sales;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SalesTable extends Component implements HasTable

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

    public $date_from;
    public $date_to;
    public $rowCount = 0;


    public function updateRowCount()
    {
        if ($this->date_from && $this->date_to) {
            $this->rowCount = Sales::whereBetween('created_at', [
                Carbon::parse($this->date_from)->startOfDay(),
                Carbon::parse($this->date_to)->endOfDay()
            ])->count();
        } else {
            $this->rowCount = Sales::count();
        }
    }

    public function exportSales()
    {
        if (!$this->date_from || !$this->date_to) {
            session()->flash('error', 'Please select both "From" and "To" dates.');
            return;
        }

        $start = Carbon::parse($this->date_from)->startOfDay();
        $end = Carbon::parse($this->date_to)->endOfDay();

        if ($start->gt($end)) {
            session()->flash('error', '"From" date must be earlier than "To" date.');
            return;
        }

        $sales = Sales::with(['category', 'subcategory'])
            ->whereBetween('created_at', [$start, $end])
            ->get();

        if ($sales->isEmpty()) {
            session()->flash('error', 'No sales records found for the selected date range.');
            return;
        }

        $templatePath = storage_path('app/templates/SalesReport.xlsx');
        if (!file_exists($templatePath)) {
            session()->flash('error', 'SalesReport.xlsx template not found.');
            return;
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $row = 9;
        foreach ($sales as $sale) {
            $sheet->setCellValue("A{$row}", $sale->order_id);
            $sheet->setCellValue("B{$row}", $sale->jo_number);
            $sheet->setCellValue("C{$row}", $sale->name);
            $sheet->setCellValue("D{$row}", $sale->category->category_name ?? 'N/A');
            $sheet->setCellValue("E{$row}", $sale->subcategory->subcategory_name ?? 'N/A');
            $sheet->setCellValue("F{$row}", $sale->qty);
            $sheet->setCellValue("G{$row}", $sale->price);
            $sheet->setCellValue("H{$row}", $sale->payment_status);
            $sheet->setCellValue("I{$row}", $sale->amount);
            $row++;
        }

        $filename = "Sales_Export_" . now()->format('Ymd_His') . ".xlsx";
        $directory = storage_path('app/reports');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . '/' . $filename;
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }


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

    private function getLatestPayment($order_id, $subcategory_id)
{
    return \App\Models\payment::where('order_id', $order_id)
        ->where('subcategory_id', $subcategory_id)
        ->orderByDesc('payment_date')
        ->orderByDesc('id')
        ->first();
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

     public $exportType = '';


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

    // public function update()
    // {
    //     try{
    //     $this->validate(
    //         [
    //         'editOrders.qty' => 'required|numeric|min:1',
    //         'editOrders.price' => 'required|numeric',
    //         'editOrders.amount' => 'required|numeric',
    //         'editOrders.total' => 'required|numeric',
    //         'editOrders.deadline' => 'required|date',
    //         'editOrders.status' => 'required|string|max:255',
    //         'editOrders.remarks' => 'required|string|max:255',
    //         'editOrders.isActive' => 'required|boolean', 
    //         ]
    //     );

    //     $order = Order::find($this->editOrders['id']);

    //     if ($order) {
    //         $order->update([
    //             'qty' => $this->editOrders['qty'],
    //             'price' => $this->editOrders['price'],
    //             'amount' => $this->editOrders['amount'],
    //             'total' => $this->editOrders['total'],
    //             'deadline' => $this->editOrders['deadline'],
    //             'status' => $this->editOrders['status'],
    //             'remarks' => $this->editOrders['remarks'],
    //             'isActive' => $this->editOrders['isActive'],
    //             'updated_by' => Auth::user()->username,
    //         ]);

             
    //         $latestPayment = payment::where('order_id', $order->order_id)
    //             ->where('subcategory_id', $order->subcategory_id)
    //             ->latest()
    //             ->first();

    //         if (
    //             $this->editOrders['status'] === 'Completed' &&
    //             $latestPayment &&
    //             $latestPayment->balance == 0.00
    //         ) {

    //                         // Get Subcategory Name
    //             $subcategory = SubCategory::find($order->subcategory_id);
    //             $subcatName = $subcategory ? Str::slug($subcategory->subcategory_name, '_') : 'Unknown';

    //             // Count existing sales for this subcategory to increment the number
    //             $count = sales::where('subcategory_id', $order->subcategory_id)->count() + 1;

    //             // Format sales_id like: Sales_TShirt_001
    //             $sales_id = 'Sales_' . $subcatName . '_' . str_pad($count, 3, '0', STR_PAD_LEFT);
              
    //                 sales::create([
    //                 'order_id' => $order->order_id,
    //                 'jo_number' => $order->jo_number,
    //                 'name' => $order->name,
    //                 'contact_no' => $order->contact_no,
    //                 'address' => $order->address,
    //                 'category_id' => $order->category_id,
    //                 'subcategory_id' => $order->subcategory_id,
    //                 'qty' => $order->qty,
    //                 'price' => $order->price,
    //                 'amount' => $order->amount,
    //                 'total' => $order->total,
    //                 'payment' => $latestPayment->payment,
    //                 'balance' => $latestPayment->balance,
    //                 'payment_status' => $latestPayment->payment_status,
    //                 'completed_at' => now(),
    //                 'added_by' => Auth::user()->username,
    //             ]);
    //         }

           

    //         $this->emit('refreshTable');
    //         $this->isEditModalOpen = false;
    //         $this->dispatchBrowserEvent('closeEditModal');
    //         session()->flash('messageUpdate', 'Order updated successfully.');
    //     } 
    // }catch (\Exception $e) {
    //         session()->flash('errorUpdate', 'Failed to save order: ' . $e->getMessage());
    //     }
    // }

    
    public function closeModal()
    {
        $this->isOpen = false;
        $this->step = 1;


    }


    
//for the date filter
    public $filterDate;
    public $dateRange;
    

    // public function edit($id)
    // {
    //     $order = Order::find($id);

    //     if ($order) {
    //         $this->editOrders = [
    //             'id' => $order->id,
    //             'category_id' => $order->category_id,
    //             'subcategory_id' => $order->subcategory_id,
    //             'qty' => $order->qty,
    //             'price' => $order->price,
    //             'amount' => $order->amount,
    //             'payment' => $order->payment,
    //             'balance' => $order->balance,
    //             'total' => $order->total,
    //             'deadline' => $order->deadline,
    //             'status' => $order->status,
    //             'remarks' => $order->remarks,
    //             'isActive' => $order->isActive,
    //         ];

    //         $this->isEditModalOpen = true;
    //     } else {
    //         session()->flash('error', 'Order not found.');
    //     }
    // }

    public function cancelEdit()
    {
        $this->reset('editOrders');
        $this->isEditModalOpen = false;
    }

    public function getTableQuery()
    {
        return sales::where('isActive', 1);
    }

    public function getFilteredRecords()
    {
        return sales::where('isActive', 1)
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
        $query = sales::query();

         $this->updateRowCount();

        if ($this->filterActivation !== '') {
            $query->where('isActive', $this->filterActivation);
        } else {
            $query->where('isActive', 1);
        }

         if ($this->date_from) {
        $query->where(function ($q) {
            $q->whereDate('created_at', '>=', $this->date_from);
        });
    }
    if ($this->date_to) {
        $query->where(function ($q) {
            $q->whereDate('created_at', '<=', $this->date_to);
        });
    }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                 $q->where('order_id', 'like', "%{$this->search}%")
                      ->orwhere('name', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.sales-table', [
            'records' => $query->get(),
            'rowCount' => $query->count(),
        ]);
    }
}

