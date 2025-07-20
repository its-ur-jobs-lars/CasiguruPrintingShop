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
use Illuminate\Support\Facades\DB;

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
    $firstReceipt = RequestReceipt::where('order_receipt_id', $orderReceiptId)->firstOrFail();
    $orderId = $firstReceipt->order_id;

    // ✅ Get only the latest request receipt per subcategory_id for this order_id
    $latestReceipts = RequestReceipt::where('order_id', $orderId)
        ->join(DB::raw('(SELECT MAX(id) as max_id FROM order_receipts WHERE order_id = "' . $orderId . '" GROUP BY subcategory_id) as latest'), function ($join) {
            $join->on('order_receipts.id', '=', 'latest.max_id');
        })
        ->select('order_receipts.*')
        ->get();

    if ($latestReceipts->isEmpty()) {
        abort(404, 'No receipts found for this Order ID.');
    }

    // ✅ Load template
    $templatePath = storage_path('app/templates/requestReceipt.xlsx');
    if (!file_exists($templatePath)) {
        abort(404, 'Excel template not found.');
    }

    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();

    // ✅ Fill spreadsheet with latest receipts
    $this->fillSpreadsheet($sheet, $latestReceipts);

    $filename = $this->generateFilename($firstReceipt, 'xlsx');
    $directory = storage_path('app/receipts');
    if (!file_exists($directory)) mkdir($directory, 0755, true);
    $filePath = "{$directory}/{$filename}";

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

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


//for the date filter
    public $filterDate;
    public $dateRange;
    public $date_from;
    public $date_to;
    



private function fillSpreadsheet($sheet, $items)
{
    $first = $items->first();

    // Header info
    $sheet->setCellValue('B6', $first->name);
    $sheet->setCellValue('B7', $first->address);
    $sheet->setCellValue('G6', $first->payment_date);
    $sheet->setCellValue('G7', $first->contact_no);
    $sheet->setCellValue('B5', $first->receipt_number);

    $startRow = 11;

    // Initialize totals
    $totalAmount = 0;
    $totalLayout = 0;

    foreach ($items as $item) {
        $categoryName = $item->category?->category_name ?? 'N/A';
        $subcategoryName = $item->subcategory?->subcategory_name ?? 'N/A';

        $sheet->setCellValue("A{$startRow}", $item->order_id);
        $sheet->setCellValue("B{$startRow}", $item->qty);
        $sheet->setCellValue("C{$startRow}", $categoryName);
        $sheet->setCellValue("E{$startRow}", $subcategoryName);
        $sheet->setCellValue("G{$startRow}", $item->price);
        $sheet->setCellValue("I{$startRow}", $item->amount);

        // Sum total amount and layout fee
        $totalAmount += (float) $item->amount;
        $totalLayout += (float) ($item->layout_fee ?? 0);

        $startRow++;
    }

    // Calculate grand total
    $grandTotal = $totalAmount + $totalLayout;

    // Fill totals
    // $sheet->setCellValue("I25", (float) $totalAmount);    // Total item amount
    // $sheet->setCellValue("I26", (float) $totalLayout);    // Total layout fee
    $sheet->setCellValue("I26", (float) $grandTotal);     // Grand total (amount + layout)

    $sheet->setCellValue("I27", (float) $first->payment); // Payment
    $sheet->setCellValue("I28", (float) $first->balance); // Balance

    $sheet->setCellValue("B28", (float) $totalLayout);    // Total layout fee
    $sheet->setCellValue("B30", $first->payment_method);
    $sheet->setCellValue("B31", $first->reference_number);
    $sheet->setCellValue("B32", $first->date);
    $sheet->setCellValue("I30", $first->payment_status);
    $sheet->setCellValue("I31", $first->remarks);
    $sheet->setCellValue("I32", $first->service_by);

    $sheet->setCellValue("B39", strtoupper($first->name));
    $sheet->setCellValue("G39", strtoupper($first->service_by));
}





// Helper: generate filename
private function generateFilename($receipt, $ext = 'xlsx')
{
    $customerName = preg_replace('/[^A-Za-z0-9\-]/', '_', $receipt->name);
    $paymentDate = \Carbon\Carbon::parse($receipt->payment_date)->format('Y-m-d');
    return "{$customerName}_{$paymentDate}.{$ext}";
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
        return RequestReceipt::where('isActive', 1)
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

