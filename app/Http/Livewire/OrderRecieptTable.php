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
use App\Models\RequestReceipt;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Dompdf\Dompdf;
use Dompdf\Options;


class OrderRecieptTable extends Component implements HasTable
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
        'date' => null,
        'payment_method' => null,
        'reference_number' => null,
        'payment_date' => null,
        'payment_status' => null,
        'status' => null,
        'remarks' => null,
        'isActive' => null,
    ];
public $order_id;
    
public $selectedpayment;

public function updatedSelectedpayment($value)
{
    if ($value === 'Partial') {
        // Fetch the order using the selected order_id
        $order = \App\Models\Order::where('order_id', $this->order_id)->first();
        $this->editOrders['balance'] = $order ? $order->balance : 0;
        $this->editOrders['payment_status'] = 'Partial';
    } elseif ($value === 'Paid') {
        $this->editOrders['payment_status'] = 'Paid';
        $this->editOrders['balance'] = 0;
    } elseif ($value === 'Unpaid') {
        $this->editOrders['payment_status'] = 'Unpaid';
        $this->editOrders['balance'] = 0;
    } else {
        $this->editOrders['payment_status'] = null;
        $this->editOrders['balance'] = 0;
    }
}

    public function rules()
    {
        return [
            'editOrders.date' => 'required|date',
            'editOrders.payment_method' => 'nullable|string|max:255',
            'editOrders.reference_number' => 'nullable|string|max:255',
            'editOrders.payment_date' => 'nullable|date',
            'editOrders.payment_status' => 'nullable|string|max:255',
            'editOrders.status' => 'required|string|max:255',
            'editOrders.remarks' => 'required|string|max:255',
            'editOrders.isActive' => 'required|boolean',
        ];
    }

    // public function updated($field)
    // {
    //     $category_id = $this->editOrders['category_id'] ?? null;
    //     $subcategory_id = $this->editOrders['subcategory_id'] ?? null;
    //     $qty = (float)($this->editOrders['qty'] ?? 0);

    //     if (in_array($field, ['editOrders.category_id', 'editOrders.subcategory_id', 'editOrders.qty'])) {
    //         if ($category_id && $subcategory_id && $qty) {
    //             $price = $this->getPriceFromPricelist($category_id, $subcategory_id, $qty);
    //             $amount = $price * $qty;

    //             $this->editOrders['price'] = $price;
    //             $this->editOrders['amount'] = $amount;
    //             $this->editOrders['total'] = $amount;
    //             $this->editOrders['balance'] = $amount;
    //         }
    //     }

    //     if (in_array($field, ['editOrders.amount', 'editOrders.downpayment'])) {
    //         $amount = (float)($this->editOrders['amount'] ?? 0);
    //         $downpayment = (float)($this->editOrders['downpayment'] ?? 0);
    //         $balance = max($amount - $downpayment, 0);

    //         $this->editOrders['total'] = $amount;
    //         $this->editOrders['balance'] = $balance;
    //     }
    // }

    // private function getPriceFromPricelist($category_id, $subcategory_id, $qty)
    // {
    //     $pricelist = Pricelist::where('category_id', $category_id)
    //         ->where('subcategory_id', $subcategory_id)
    //         ->first();

    //     if (!$pricelist) {
    //         return 0;
    //     }

    //     if ($qty >= 10 && $qty <= 50) {
    //         return $pricelist->price_10_50;
    //     } elseif ($qty >= 51 && $qty <= 100) {
    //         return $pricelist->price_51_100;
    //     } elseif ($qty >= 101 && $qty <= 500) {
    //         return $pricelist->price_101_500;
    //     } else {
    //         return $pricelist->price_10_50;
    //     }
    // }

     public function closeModal()
    {
        $this->isOpen = false;
        $this->step = 1;

    }

    public function update()
    {
        $this->validate(
            [
           'editOrders.date' => 'required|date',
            'editOrders.payment_method' => 'nullable|string|max:255',
            'editOrders.reference_number' => 'nullable|string|max:255',
            'editOrders.payment_date' => 'nullable|date',
            'editOrders.payment_status' => 'nullable|string|max:255',
            'editOrders.status' => 'required|string|max:255',
            'editOrders.remarks' => 'required|string|max:255',
            'editOrders.isActive' => 'required|boolean',

            ]
        );

        $order = RequestReceipt::find($this->editOrders['id']);

        if ($order) {
            $order->update([
                'date' => $this->editOrders['date'],
                'payment_method' => $this->editOrders['payment_method'],
                'reference_number' => $this->editOrders['reference_number'],
                'payment_date' => $this->editOrders['payment_date'],
                'payment_status' => $this->editOrders['payment_status'],
                'status' => $this->editOrders['status'],
                'remarks' => $this->editOrders['remarks'],
                'isActive' => $this->editOrders['isActive'],
                'updated_by' => Auth::user()->username,
            ]);

            // Also update the related order (if exists)
        $order = Order::where('order_id', $order->order_id)->first();
        if ($order) {
            $order->update([
                'status' => $this->editOrders['status'],
                'remarks' => $this->editOrders['remarks'],
            ]);
        }

            $this->emit('refreshTable');
            $this->isEditModalOpen = false;
            $this->dispatchBrowserEvent('closeEditModal');
            session()->flash('messageUpdate', 'Order Request is updated successfully.');
        } else {
            session()->flash('error', 'Order Request not found.');
        }
    }

    public function edit($id)
    {
        $order = RequestReceipt::find($id);

        if ($order) {
            $this->editOrders = [
                'id' => $order->id,
                'date' => $order->date,
                'payment_method' => $order->payment_method,
                'reference_number' => $order->reference_number,
                'payment_date' => $order->payment_date,
                'payment_status' => $order->payment_status,
                'status' => $order->status,
                'remarks' => $order->remarks,
                'isActive' => $order->isActive,
            ];

            $this->isEditModalOpen = true;
        } else {
            session()->flash('error', 'Order not found.');
        }
    }

    public function exportExcel($orderReceiptId)
{
    $receipt = RequestReceipt::where('order_receipt_id', $orderReceiptId)->firstOrFail();
    $spreadsheet = IOFactory::load(storage_path('app/templates/requestReceipt.xlsx'));
    $sheet = $spreadsheet->getActiveSheet();

    // Fill in data as before
    $this->fillSpreadsheet($sheet, $receipt);

    $filename = $this->generateFilename($receipt, 'xlsx');
    $directory = storage_path('app/receipts');
    if (!file_exists($directory)) mkdir($directory, 0755, true);
    $filePath = "{$directory}/{$filename}";

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    return response()->download($filePath)->deleteFileAfterSend();
}

   public function exportExcelMultiple($orderId)
{
    // Fetch all items for the given order_id
    $items = RequestReceipt::where('order_id', $orderId)->get();

    if ($items->isEmpty()) {
        abort(404, 'No receipt found for the provided Order ID.');
    }

    // Load Excel template
    $spreadsheet = IOFactory::load(storage_path('app/templates/requestReceipt.xlsx'));
    $sheet = $spreadsheet->getActiveSheet();

    // Start filling from row 2 (assuming row 1 is header)
    $startRow = 2;

    foreach ($items as $index => $item) {
        $row = $startRow + $index;

        // Fill Excel sheet columns (adjust based on your template)
        $sheet->setCellValue("A{$row}", $index === 0 ? $item->order_id : ''); // show order_id only on first row
        $sheet->setCellValue("B{$row}", $item->item_name);
        $sheet->setCellValue("C{$row}", $item->category);
        $sheet->setCellValue("D{$row}", $item->quantity);
        $sheet->setCellValue("E{$row}", $item->unit_price);
        $sheet->setCellValue("F{$row}", $item->total_price);
    }

    // Save to file
    $filename = "Order_{$orderId}_Receipt.xlsx";
    $directory = storage_path('app/receipts');
    if (!file_exists($directory)) mkdir($directory, 0755, true);
    $filePath = "{$directory}/{$filename}";

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    // Return downloadable response
    return response()->download($filePath)->deleteFileAfterSend();
}


public function exportPDF($orderReceiptId)
{
    $receipt = RequestReceipt::where('order_receipt_id', $orderReceiptId)->firstOrFail();
    $spreadsheet = IOFactory::load(storage_path('app/templates/requestReceipt.xlsx'));
    $sheet = $spreadsheet->getActiveSheet();

    // Fill in data
    $this->fillSpreadsheet($sheet, $receipt);

    // Convert to HTML
    $htmlWriter = new Html($spreadsheet);
    ob_start();
    $htmlWriter->save('php://output');
    $htmlContent = ob_get_clean();

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($htmlContent);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = $this->generateFilename($receipt, 'pdf');
    $directory = storage_path('app/receipts');
    if (!file_exists($directory)) mkdir($directory, 0755, true);
    $filePath = "{$directory}/{$filename}";

    file_put_contents($filePath, $dompdf->output());

    return response()->download($filePath)->deleteFileAfterSend();
}

// // Helper: fill spreadsheet with data
// private function fillSpreadsheet($sheet, $receipt)
// {
//     $sheet->setCellValue('B6', $receipt->name);
//     $sheet->setCellValue('B7', $receipt->address);
//     $sheet->setCellValue('G6', $receipt->payment_date);
//     $sheet->setCellValue('G7', $receipt->contact_no);
//     $sheet->setCellValue('G8', $receipt->order_id);
//     $sheet->setCellValue('C5', $receipt->order_receipt_id);
//     $startRow = 11;

//     $items = \App\Models\RequestReceipt::with(['category', 'subcategory'])
//         ->where('order_receipt_id', $receipt->order_receipt_id)
//         ->get();

//     foreach ($items as $item) {
//         $categoryName = $item->category ? $item->category->category_name : 'N/A';
//         $subcategoryName = $item->subcategory ? $item->subcategory->subcategory_name : 'N/A';

//         $sheet->setCellValue("A{$startRow}", $item->qty);
//         $sheet->setCellValue("B{$startRow}", $categoryName);
//         $sheet->setCellValue("C{$startRow}", $subcategoryName);
//         $sheet->setCellValue("E{$startRow}", $item->price);
//         $sheet->setCellValue("G{$startRow}", $item->amount);
//         $startRow++;
//     }


//     $sheet->setCellValue("G26", (float)$receipt->total);
//     $sheet->setCellValue("G27", (float)$receipt->payment);
//     $sheet->setCellValue("G28", (float)$receipt->balance);
//     $sheet->setCellValue("C30", $receipt->payment_method);
//     $sheet->setCellValue("C31", $receipt->reference_number);
//     $sheet->setCellValue("C32", $receipt->date);
//     $sheet->setCellValue("G30", $receipt->payment_status);
//     $sheet->setCellValue("G31", $receipt->remarks);
//     $sheet->setCellValue("G32", $receipt->service_by);
// }

//for the date filter
    public $filterDate;
    public $dateRange;
    public $date_from;
    public $date_to;
    


private function fillSpreadsheet($sheet, $receipt)
{
    $sheet->setCellValue('B6', $receipt->name);
    $sheet->setCellValue('B7', $receipt->address);
    $sheet->setCellValue('G6', $receipt->payment_date);
    $sheet->setCellValue('G7', $receipt->contact_no);
    $sheet->setCellValue('B5', $receipt->order_receipt_id);

    
    $startRow = 11;

    $items = \App\Models\RequestReceipt::with(['category', 'subcategory'])
        ->where('order_receipt_id', $receipt->order_receipt_id)
        ->get();

    foreach ($items as $item) {
        $categoryName = $item->category ? $item->category->category_name : 'N/A';
        $subcategoryName = $item->subcategory ? $item->subcategory->subcategory_name : 'N/A';

        $sheet->setCellValue("A{$startRow}", $item->order_id);
        $sheet->setCellValue("B{$startRow}", $item->qty);
        $sheet->setCellValue("C{$startRow}", $categoryName);
        $sheet->setCellValue("E{$startRow}", $subcategoryName);
        $sheet->setCellValue("G{$startRow}", $item->price);
        $sheet->setCellValue("I{$startRow}", $item->amount);
        $startRow++;
    }

    $sheet->setCellValue("I26", (float)$receipt->total);
    $sheet->setCellValue("I27", (float)$receipt->payment);
    $sheet->setCellValue("I28", (float)$receipt->balance);
    $sheet->setCellValue("B30", $receipt->payment_method);
    $sheet->setCellValue("B31", $receipt->reference_number);
    $sheet->setCellValue("B32", $receipt->date);
    $sheet->setCellValue("I30", $receipt->payment_status);
    $sheet->setCellValue("I31", $receipt->remarks);
    $sheet->setCellValue("I32", $receipt->service_by);

    $sheet->setCellValue("B39", strtoupper($receipt->name));
    $sheet->setCellValue("G39", strtoupper($receipt->service_by));



}



// Helper: generate filename
private function generateFilename($receipt, $ext = 'xlsx')
{
    $customerName = preg_replace('/[^A-Za-z0-9\-]/', '_', $receipt->name);
    $paymentDate = \Carbon\Carbon::parse($receipt->payment_date)->format('Y-m-d');
    return "{$customerName}_{$paymentDate}.{$ext}";
}


    
//    public function export($orderReceiptId)
// {
//     // Fetch the receipt by order_receipt_id
//     $receipt = RequestReceipt::where('order_receipt_id', $orderReceiptId)->firstOrFail();

//     // Load the template Excel file
//     $templatePath = storage_path('app/templates/requestReceipt.xlsx');
//     $spreadsheet = IOFactory::load($templatePath);
//     $sheet = $spreadsheet->getActiveSheet();

//     // Fill in the cells
//     $sheet->setCellValue('B6', $receipt->name);
//     $sheet->setCellValue('B7', $receipt->address);
//     $sheet->setCellValue('G6', $receipt->payment_date);
//     $sheet->setCellValue('G7', $receipt->contact_no);
//     $sheet->setCellValue('G8', $receipt->order_id);
//     $sheet->setCellValue('C5', $receipt->order_receipt_id);

//     // Order items from row 11
//     $startRow = 11;
//     foreach (RequestReceipt::where('order_id', $receipt->order_id)->get() as $item) {
//         $sheet->setCellValue("A{$startRow}", $item->qty);
//         $sheet->setCellValue("B{$startRow}", $item->category_id);
//         $sheet->setCellValue("C{$startRow}", $item->subcategory_id);
//         $sheet->setCellValue("E{$startRow}", $item->price);
//         $sheet->setCellValue("G{$startRow}", $item->amount);
//         $startRow++;
//     }

//     // Summary
//     $sheet->setCellValue("G26", (float)$receipt->total);
//     $sheet->setCellValue("G27", (float)$receipt->payment);
//     $sheet->setCellValue("G28", (float)$receipt->balance);

//     $sheet->setCellValue("C30", $receipt->payment_method);
//     $sheet->setCellValue("C31", $receipt->reference_number);
//     $sheet->setCellValue("C32", $receipt->date);

//     $sheet->setCellValue("G30", $receipt->payment_status);
//     $sheet->setCellValue("G31", $receipt->remarks);
//     $sheet->setCellValue("G32", $receipt->service_by);

//     // ✅ Create a clean filename based on customer name and date
//     $customerName = preg_replace('/[^A-Za-z0-9\-]/', '_', $receipt->name);
//     $paymentDate = \Carbon\Carbon::parse($receipt->payment_date)->format('Y-m-d');

//     $filename = "{$customerName}_{$paymentDate}.xlsx";
//     $directory = storage_path('app/receipts');

//     // ✅ Ensure the folder exists
//     if (!file_exists($directory)) {
//         mkdir($directory, 0755, true);
//     }

//     $filePath = $directory . '/' . $filename;

//     // Save the spreadsheet
//     $writer = new Xlsx($spreadsheet);
//     $writer->save($filePath);

//     // Return the file as a download
//     return response()->download($filePath)->deleteFileAfterSend();
// }


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
        return RequestReceipt::query()
            ->when($this->filterActivation !== '', fn($query) => $query->where('isActive', $this->filterActivation))
            ->when($this->search !== '', function ($query) {
                return $query->where(function ($q) {
                    $q->where('order_receipt_id', 'like', "%{$this->search}%")
                      ->orwhere('name', 'like', "%{$this->search}%")
                      ->orwhere('order_id', 'like', "%{$this->search}%");
                });
            })->get();
    }

    public function getRowCountProperty()
    {
        return $this->getFilteredRecords()->count();
    }

    public function render()
    {
        $query = RequestReceipt::query();

        if ($this->filterActivation !== '') {
            $query->where('isActive', $this->filterActivation);
        } else {
            $query->where('isActive', 1);
        }

        if ($this->date_from) {
        $query->where(function ($q) {
            $q->whereDate('date', '>=', $this->date_from);
        });
    }
    if ($this->date_to) {
        $query->where(function ($q) {
            $q->whereDate('date', '<=', $this->date_to);
        });
    }


        if (!empty($this->search)) {
            $query->where(function ($q) {
                 $q->where('order_receipt_id', 'like', "%{$this->search}%")
                    ->orwhere('order_id', 'like', "%{$this->search}%")
                      ->orwhere('name', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.order-reciept-table', [
            'records' => $query->get(),
            'rowCount' => $query->count(),
        ]);
    }
}

