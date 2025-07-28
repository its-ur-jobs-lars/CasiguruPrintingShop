<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\Expmonitoring;
use App\Models\supplierInv;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;

class ExpenseTable extends Component implements HasTable
{
    use WithPagination, InteractsWithTable {
        WithPagination::resetPage insteadof InteractsWithTable;
    }

     protected $listeners = ['refreshComponent' => '$refresh'];
    public $isOpen = false;
    public $step = 1;
    public $search = '';
    public $filterActivation = 'all';
    public $editId = null;
    public $suppliers = [];

    public $editExpenses = [
        'id' => null,
        'date' => null,
        'si_or_no' => null,
        'supplier_id' => null,
        'particular' => null,
        'amount' => 0,
        'qty' => 1,
        'subtotal' => 0,
        'remarks' => null,
        'added_by' => null,
        'updated_by' => null,
        'isActive' => 1,
    ];

    protected $rules = [
        'editExpenses.id' => 'required|exists:expenses,id',
        'editExpenses.date' => 'required|date',
        'editExpenses.si_or_no' => 'required|string|max:255',
        'editExpenses.supplier_id' => 'required|exists:suppliers,id',
        'editExpenses.particular' => 'required|string|max:255',
        'editExpenses.amount' => 'required|numeric|min:0',
        'editExpenses.qty' => 'required|numeric|min:1',
        'editExpenses.subtotal' => 'required|numeric|min:0',
        'editExpenses.remarks' => 'nullable|string|max:255',
        'editExpenses.isActive' => 'required|in:0,1',
    ];

   public $supplier = [];
   public $Suppliers = [];
   public $editSupplier = [];

    public function getTableQuery()
    {
        return Expmonitoring::query();
    }

    public function getRowCountProperty()
    {
        $query = Expmonitoring::query();

        if ($this->filterActivation !== 'all') {
            $query->where('isActive', $this->filterActivation);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('particular', 'like', "%{$this->search}%")
                  ->orWhere('si_or_no', 'like', "%{$this->search}%");
            });
        }

        return $query->count();
    }

    public function render()
    {
        $query = Expmonitoring::query();

        if ($this->filterActivation !== 'all') {
            $query->where('isActive', $this->filterActivation);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('particular', 'like', "%{$this->search}%")
                  ->orWhere('si_or_no', 'like', "%{$this->search}%");
            });
        }

        $records = $query->paginate(10);
        $rowCount = $query->count();

        return view('livewire.expense-table', [
            'records' => $records,
            'rowCount' => $rowCount,
        ]);
    }

   public function mount(){
    $this->Suppliers = supplierInv::where('is_active', 1)->get();
   $this->supplier = supplierInv::pluck('name', 'supplier_id')->toArray();
   }
    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal1()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public $data = [
        'qty' => 1,
        'amount' => 0,
        'subtotal' => 0,
    ];

    public function resetForm()
    {
        $this->editExpenses = [
            'id' => '',
            'date' => '',
            'si_or_no' => '',
            'supplier_id' => '',
            'particular' => '',
            'amount' => 0,
            'qty' => 1,
            'subtotal' => 0,
            'remarks' => '',
            'updated_by' => '',
            'isActive' => 1,
        ];
        $this->editId = null;
    }

  public function update()
{
    try {
        $this->validate([
            'editExpenses.date' => 'required|date',
            'editExpenses.si_or_no' => 'required|string|max:255',
            'editExpenses.supplier_id' => 'required|exists:suppliers,supplier_id',
            'editExpenses.particular' => 'required|string|max:255',
            'editExpenses.amount' => 'required|numeric|min:0',
            'editExpenses.qty' => 'required|numeric|min:1',
            'editExpenses.subtotal' => 'required|numeric|min:0',
            'editExpenses.remarks' => 'nullable|string|max:255',
            'editExpenses.isActive' => 'required|in:0,1',
        ]);

        $expenseId = $this->editExpenses['id'] ?? null;

        if (!$expenseId) {
            session()->flash('error', 'Missing expense ID.');
            return;
        }

        $expensesDetails = Expmonitoring::find($expenseId);

        if (!$expensesDetails) {
            session()->flash('error', 'Expense record not found.');
            return;
        }

        $expensesDetails->update([
            'date' => $this->editExpenses['date'],
            'si_or_no' => $this->editExpenses['si_or_no'],
            'supplier_id' => $this->editExpenses['supplier_id'],
            'particular' => $this->editExpenses['particular'],
            'amount' => $this->editExpenses['amount'],
            'qty' => $this->editExpenses['qty'],
            'subtotal' => $this->editExpenses['subtotal'],
            'remarks' => $this->editExpenses['remarks'],
            'isActive' => $this->editExpenses['isActive'],
            'updated_by' => Auth::user()->username,
        ]);

        // Emit an event to refresh the table
        $this->emit('refreshTable');

        // Close the modal
        $this->isEditModalOpen = false;

        // Show a success message
        session()->flash('messageUpdate', 'Government Payable is updated successfully.');

        // Dispatch a browser event to close the modal
        $this->dispatchBrowserEvent('closeEditModal');

    } catch (\Illuminate\Validation\ValidationException $e) {
        session()->flash('error', 'Validation failed: ' . json_encode($e->errors()));
        return;
    } catch (\Exception $e) {
        session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
        return;
    }
}


  public $isEditModalOpen = '';

    
public function edit($id)
{
    $expensesDetails = Expmonitoring::find($id);

    if ($expensesDetails) {
        $this->editExpenses = [
            'id' => $expensesDetails->id,
            'date' => $expensesDetails->date,
            'si_or_no' => $expensesDetails->si_or_no,
            'supplier_id' => $expensesDetails->supplier_id,
            'particular' => $expensesDetails->particular,
            'amount' => $expensesDetails->amount,
            'qty' => $expensesDetails->qty,
            'subtotal' => $expensesDetails->subtotal,
            'remarks' => $expensesDetails->remarks,
            'isActive' => $expensesDetails->isActive,
        ];

        // ✅ Retrieve supplier name from supplierInv table
        $supplier = supplierInv::where('supplier_id', $expensesDetails->supplier_id)->first();
        $this->editSupplierName = $supplier ? $supplier->name : 'Not Available';

        
        $this->isEditModalOpen = true;

    } else {
        session()->flash('error', 'Record not found.');
    }
}

 
    public function nextStep()
    {
        // Auto-calculate subtotal (qty * amount)
        $this->data['subtotal'] = $this->data['qty'] * $this->data['amount'];
        $this->step = 2;
    }

     public function nextStep1(){
        $this->step = 3;
    }

    public function previousStep(){
        $this->step = 1;
    }

}
