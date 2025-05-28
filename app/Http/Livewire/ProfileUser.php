<?php

namespace App\Http\Livewire;


use Livewire\Component;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TextFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\WithPagination;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileUser extends Component
{
    // In ProfileUser.php
    protected $listeners = ['passwordChanged'];

    public function render()
    {
          $query = User::query();

         // Fetch data with pagination
        $record = $query->paginate(10);

        return view('livewire.profile-user', [
            'lastPasswordChange' => $this->lastPasswordChange,
             'records' => $record,
        ]);
    }

    public function passwordChanged($timestamp)
{
    $this->lastPasswordChange = \Carbon\Carbon::parse($timestamp);
}

    public $modalUserName = '';
    public $lastPasswordChange;
    public $isChangePasswordModalOpen = false;
    public $passwordData = [
        'new_password' => '',
        'confirm_password' => '',
    ];


    public function mount()
{
    $user = Auth::user();
    $this->editUser = [
        'firstname' => $user->firstname,
        'middlename' => $user->middlename,
        'lastname' => $user->lastname,
        'ext_name' => $user->ext_name,
        'username' => $user->username,
        'role' => $user->role,
        'employee_number' => $user->employee_number,
        // Add other fields as needed
    ];

     $this->lastPasswordChange = $user->updated_at;
}

    //for edit
   public $editUser = [
    'id' => null,
    'lastname' => null,
    'firstname' => null,
    'middlename' => null,
    'ext_name' => null,
    'username' => null,
    'employee_number' => null,
    'division_id' => null,
    'department_id' => null,
    'role' => null,
    'isActive' => null,
];

//for editing
 protected $rules = [
        'editUser.firstname' => 'required|string|max:20',
        'editUser.lastname' => 'required|string|max:255',
        'editUser.middlename' => 'required|string|max:50',
        'editUser.ext_name' => 'required|string|max:50',
        'editUser.username' => 'required|string|max:255',
        'editUser.employee_number' => 'required|string|max:255',
        'editUser.division_id' => 'required|string|max:20',
        'editUser.department_id' => 'required|string|max:20',
        'editUser.role' => 'required|string|max:255',
        'editUser.isActive' => 'required|string|max:255'

    ];



//for update
public function update()
{
    $this->validate([ // Apply validation rules
         'editUser.firstname' => 'required|string|max:20',
        'editUser.lastname' => 'required|string|max:255',
        'editUser.middlename' => 'required|string|max:50',
        'editUser.ext_name' => 'required|string|max:50',
        'editUser.username' => 'required|string|max:255',
        'editUser.employee_number' => 'required|string|max:255',
        'editUser.division_id' => 'required|string|max:20',
        'editUser.department_id' => 'required|string|max:20',
        'editUser.role' => 'required|string|max:255',
        'editUser.isActive' => 'required|string|max:255'
    ]);

    // Find the product by ID
    $userdetails = User::find($this->editUser['id']);

    if ($userdetails) {
        $userdetails->update([
            'lastname' => $this->editUser['lastname'],
            'firstname' => $this->editUser['firstname'],
            'middlename' => $this->editUser['middlename'],
            'ext_name' => $this->editUser['ext_name'],
            'employee_number' => $this->editUser['employee_number'],
            'diviname' => $this->editUser['ext_name'],
            'usersion_id' => $this->editUser['division_id'],
            'department_id' => $this->editUser['department_id'],
            'role' => $this->editUser['role'],
            'isActive' => $this->editUser['isActive']
        ]);

        // Emit an event to refresh the table
        $this->emit('refreshTable');

        // Close the modal
        $this->isEditModalOpen = false;


        // Show a success message
        session()->flash('messageUpdate', 'User updated successfully.');

        // Dispatch a browser event to close the modal
        $this->dispatchBrowserEvent('closeEditModal');
    } else {
        session()->flash('error', 'Product not found.');
    }
}

public function save()
{
    $user = Auth::user();
    $user->update($this->editUser);
    session()->flash('success', 'Profile updated successfully!');
}

public function edit($id)
{
    $userdetails = User::find($id);

    if ($userdetails) {
        $this->editUser = [
            'id' => $userdetails->id, // Include the product ID
            'lastname' => $userdetails->lastname,
            'firstname' => $userdetails->firstname,
            'middlename' => $userdetails->middlename,
            'ext_name' => $userdetails->ext_name,
            'username' => $userdetails->username,
            'employee_number' => $userdetails->employee_number,
            'division_id' => $userdetails->division_id,
            'department_id' => $userdetails->department_id,
            'role' => $userdetails->role,
            'isActive' => $userdetails->isActive,
        ];


        $this->isEditModalOpen = true;
    } else {
        session()->flash('error', 'Product not found.');
    }
}
public function openChangePasswordModal()
{
    $user = Auth::user();
    $this->modalUserName = $user ? $user->username : '';
    $this->passwordData['new_password'] = '';
    $this->passwordData['confirm_password'] = '';
    $this->isChangePasswordModalOpen = true;
}

    public function closeChangePasswordModal()
    {
        $this->isChangePasswordModalOpen = false;
        $this->reset('passwordData');
    }

}

    