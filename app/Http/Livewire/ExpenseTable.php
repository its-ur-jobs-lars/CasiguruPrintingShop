<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\Expmonitoring;
use App\Models\supplierInv;
use Illuminate\Support\Facades\Auth;

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

    public $data = [
        'date' => '',
        'si_or_no' => '',
        'supplier_id' => '',
        'particular' => '',
        'amount' => 0,
        'qty' => 1,
        'subtotal' => 0,
        'remarks' => '',
        'added_by' => '',
        'updated_by' => '',
        'isActive' => 1,
    ];

    public $suppliers = [];

    public function mount()
    {
        $this->suppliers = supplierInv::pluck('name', 'id');
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->data = [
            'date' => '',
            'si_or_no' => '',
            'supplier_id' => '',
            'particular' => '',
            'amount' => 0,
            'qty' => 1,
            'subtotal' => 0,
            'remarks' => '',
            'added_by' => '',
            'updated_by' => '',
            'isActive' => 1,
        ];
        $this->editId = null;
    }

    public function updated($field)
    {
        if (in_array($field, ['data.amount', 'data.qty'])) {
            $this->data['subtotal'] = $this->data['amount'] * $this->data['qty'];
        }
    }

    public function edit($id)
    {
        $expense = Expmonitoring::findOrFail($id);
        $this->editId = $expense->id;
        $this->data = $expense->toArray();
        $this->isOpen = true;
        $this->step = 1;
    }

    public function update()
    {
        $this->validate([
            'data.date' => 'required|date',
            'data.supplier_id' => 'required|exists:suppliers,id',
            'data.particular' => 'required|string|max:255',
            'data.amount' => 'required|numeric|min:0',
            'data.qty' => 'required|numeric|min:1',
        ]);

        if (!$this->editId) {
            session()->flash('error', 'No record selected for update.');
            return;
        }

        $expense = Expmonitoring::findOrFail($this->editId);
        $this->data['subtotal'] = $this->data['amount'] * $this->data['qty'];
        $this->data['updated_by'] = Auth::user()->name;

        $expense->update($this->data);

        session()->flash('messageInsert', 'Expense updated successfully.');
        $this->closeModal();
    }

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

     public $isEditModalOpen = '';

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

        $records = $query->get();
        $rowCount = $query->count();

        return view('livewire.expense-table', [
            'records' => $records,
            'rowCount' => $rowCount,
            'suppliers' => $this->suppliers, // ✅ Pass it to the view
        ]);
    }
}
