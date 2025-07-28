<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Expmonitoring;
use App\Models\supplierInv; // Assuming this is the model for suppliers
use Illuminate\Support\Facades\Auth;

class ExpensesMonitoring extends Component
{

     public $data = [
        'date' => '',
        'si_or_no' => '',
        'particular' => '',
        'qty' => 1,
        'amount' => 0,
        'subtotal' => 0,
        'remarks' => '',
    ];
    
    public $isOpen = false;
    public $step = 1;
    public $Employee;

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

    public function mount(){
         $this->Suppliers = supplierInv::where('is_active', 1)->get();
    }

    public function nextStep()
    {
        // Auto-calculate subtotal (qty * amount)
        $this->data['subtotal'] = $this->data['qty'] * $this->data['amount'];
        $this->step = 2;
    }

    public function previousStep()
    {
        $this->step = 1;
    }

    public function save()
    {
        // Basic validation (you can add more rules if needed)
        $this->validate([
                'data.date' => 'required|date',
                'data.si_or_no' => 'required|string|max:255',
                'data.particular' => 'required|string|max:255',
                'data.qty' => 'required|numeric|min:1',
                'data.amount' => 'required|numeric|min:0',
                'data.subtotal' => 'required|numeric|min:0',
                'data.remarks' => 'nullable|string|max:255',
        ]);

        try {
            Expmonitoring::create([
                'date' => $this->data['date'],
                'si_or_no' => $this->data['si_or_no'],
                'particular' => $this->data['particular'],
                'amount' => $this->data['amount'],
                'qty' => $this->data['qty'],
                'subtotal' => $this->data['subtotal'],
                'remarks' => $this->data['remarks'],
                'supplier_id' => $this->data['supplier_id'],
                 'added_by' => Auth::user()->username,
            ]);

            $this->emit('refreshComponent');
            $this->reset(['data']);
            $this->isOpen = false;
            $this->step = 1;

            session()->flash('messageInsert', 'Expense added successfully!');
        } catch (\Exception $e) {
            session()->flash('errorInsert', 'Failed to save expense: ' . $e->getMessage());
        }

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.expenses-monitoring');
    }
}
