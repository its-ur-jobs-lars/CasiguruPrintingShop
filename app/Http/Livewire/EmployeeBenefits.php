<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\employee_benefits;
use Illuminate\Support\Facades\Auth;

class EmployeeBenefits extends Component
{
    public $data = [
        'employee_number' => '',
        'employee_name' => '',
        'date' => '',
        'sss' => 0.00,
        'sss_number' => '',
        'pagibig' => 0.00,
        'pagibig_number' => '',
        'philhealth' => 0.00,
        'philhealth_number' => '',
        'added_by' => '',
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

    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
    public function nextStep2() { $this->step = 4; }
    public function previousStep1() { $this->step = 2; }

   public function updated($propertyName)
{
    if ($propertyName === 'data.employee_number') {
        $employee = Employee::where('employee_number', $this->data['employee_number'])->first();

        if ($employee) {
            // Set only first name
            $this->data['employee_name'] = $employee->emp_Firstname;
        } else {
            $this->data['employee_name'] = '';
        }
    }
}


    public function mount()
    {
        $this->Employee = Employee::where('isActive', 1)->get();
    }

    public function render()
    {
        return view('livewire.employee-benefits', [
        ]);
    }

    public function save()
    {
        try {
            // ✅ Correct validation keys
        $this->validate([
            'data.employee_number' => 'required',
            'data.employee_name' => 'required',
            'data.date' => 'nullable|date|max:255',
            'data.sss_number' => 'required',
            'data.sss' => 'required|numeric',
            'data.pagibig_number' => 'required|numeric',
            'data.pagibig' => 'required|numeric',
            'data.philhealth_number' => 'required|numeric',
            'data.philhealth' => 'required|numeric',
        ]);

           
        employee_benefits::create([
            'employee_number' => $this->data['employee_number'],
            'employee_name' => $this->data['employee_name'],
            'date' => $this->data['date'],
            'sss_number' => $this->data['sss_number'],
            'sss' => $this->data['sss'],
            'pagibig_number' => $this->data['pagibig_number'],
            'pagibig' => $this->data['pagibig'],
            'philhealth_number' => $this->data['philhealth_number'],
            'philhealth' => $this->data['philhealth'],
            'updated_by' => Auth::user()->username,
        ]);

            $this->emit('refreshComponent');
            $this->reset(['data']);
            $this->isOpen = false;
            $this->step = 1;
            session()->flash('messageInsert', 'Benefit is successfully created!');
        } catch (\Exception $e) {
            session()->flash('errorInsert', 'Failed to save order: ' . $e->getMessage());
        }
    }

    
}

