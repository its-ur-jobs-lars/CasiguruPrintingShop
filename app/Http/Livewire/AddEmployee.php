<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\NumberInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use App\Models\productModel;
use App\Models\productTypemodel;
use Illuminate\Support\Facades\Auth;
use App\Models\BrandModel;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AddEmployee extends Component
{
    public function render()
    {
        return view('livewire.add-employee');
    }

     public $data = [];


    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public bool $isOpen = false; // Controls modal visibility

    public function save()
{
    try {

   $this->validate([
    'data.emp_Lastname' => 'required',
    'data.emp_Firstname' => 'required',
    'data.emp_Middlename' => 'required',
    'data.ext_name' => 'required', 
    'data.task' => 'required', 
    'data.date_Hired' => 'required',
    'data.description' => 'required',
    'data.employee_number' => 'required', 
]);


        // Check if the user already exists by employee number
        $existingUser = Employee::where('employee_number', $this->data['employee_number'])->first();

        if ($existingUser) {
            // If the employee number exists, throw an exception
            throw new \Exception('The employee number already exists in the system.');
        }

        // Get the count of employees with the same last name (for the sequence)
            $count = \App\Models\Employee::where('emp_Lastname', $this->data['emp_Lastname'])->count() + 1;

            // Format the sequence as 4 digits
            $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);

            // Format the date
            $date = \Carbon\Carbon::parse($this->data['date_Hired'])->format('Ymd');

            // Generate the employee_id
            $employee_id = "{$this->data['emp_Lastname']}-{$sequence}-{$date}";


        // Create a new user
        Employee::create([
            'employee_id' => $employee_id,
            'emp_Lastname' => $this->data['emp_Lastname'],
            'emp_Firstname' => $this->data['emp_Firstname'],
            'emp_Middlename' => $this->data['emp_Middlename'],
            'ext_name' => $this->data['ext_name'],
            'task' => $this->data['task'],
            'date_Hired' => $this->data['date_Hired'],
            'description' => $this->data['description'],
            'employee_number' => $this->data['employee_number'],
            // 'added_by' => $this->data['added_by'],
            'added_by' => Auth::user()->username,
        ]);

        // Emit an event to refresh the table
        $this->emit('refreshComponent');

        // Reset the form data and close the modal
        $this->reset('data');
        $this->isOpen = false;

        // Show a success message
        session()->flash('messageInsert', 'Employee is added successfully!');
    } catch (\Exception $e) {
        // Handle the exception and show an error message
        session()->flash('errorInsert', $e->getMessage());
    }
}

}
