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
use App\Models\modelModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AddUser extends Component
{
    public function render()
    {
        return view('livewire.add-user');
    }



 public $data = [];

public bool $isOpen = false; // Controls modal visibility

public function previousStep(){
    $this->step = 1;
}
    public function nextStep(){
        $this->step = 2;
    }
 public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

public function save()
{
    try {
        // Validate the input data
        $this->validate([
            'data.lastname' => 'required',
            'data.firstname' => 'required',
            'data.middlename' => 'required',
            'data.ext_name' => 'nullable', // Optional field
            'data.username' => 'nullable', // Ensure username is unique
            'data.employee_number' => 'nullable', // Ensure employee number is unique
            'data.role' => 'nullable', // Ensure department exists
            'data.password' => 'required|min:8', 
          // Ensure password is at least 8 characters
        ]);


        // Check if the user already exists by employee number
        $existingUser = User::where('employee_number', $this->data['employee_number'])->first();

        if ($existingUser) {
            // If the employee number exists, throw an exception
            throw new \Exception('The employee number already exists in the system.');
        }

        // Create a new user
        User::create([
            'lastname' => $this->data['lastname'],
            'firstname' => $this->data['firstname'],
            'middlename' => $this->data['middlename'],
            'ext_name' => $this->data['ext_name'],
            'username' => $this->data['username'],
            'employee_number' => $this->data['employee_number'],
            'role' => $this->data['role'],
            'password' => Hash::make($this->data['password']),
            // 'added_by' => $this->data['added_by'],
            // 'added_by' => Auth::user()->username,
        ]);

        // Emit an event to refresh the table
        $this->emit('refreshComponent');

        // Reset the form data and close the modal
        $this->reset('data');
        $this->isOpen = false;

        // Show a success message
        session()->flash('messageInsert', 'User is added successfully!');
    } catch (\Exception $e) {
        // Handle the exception and show an error message
        session()->flash('errorInsert', $e->getMessage());
    }
}


public $showPassword = false;

}


