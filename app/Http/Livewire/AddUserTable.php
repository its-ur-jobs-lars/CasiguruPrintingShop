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
use Illuminate\Support\Facades\Hash;


class AddUserTable extends Component implements HasTable
{
 

     // use withPagination;
    // use Tables\Concerns\InteractsWithTable;

    use WithPagination, InteractsWithTable {
        WithPagination::resetPage insteadof InteractsWithTable;
    }

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function nextStep(){
        $this->step = 2;
    }

    public function previousStep(){
        $this->step = 1;
    }

//     public function loadProducts()
//    {
//     $this->products = ProductModel::query()->where('product_type_id', 9);
//    }

    public $search = '';

    public $selectedSerialNumber = '';

    public $newSerialNumber = '';

    public $updatedSelectedSerialNumber = '';

    public $confirmDelete = false;
    public $productToDelete;
    
    protected $updatesQueryString = ['search', 'filterStatus'];

 
 
    public $isEditModalOpen = '';
    public function updatedSelectedSerialNumber($value)
    { 
    if ($value == 'Not Available') {
        $this->editUser['serial_number'] = 'Not Available';
    }
    }

      public function confirmDelete($id){
            $this->productToDelete = $id;
            $this->confirmDelete = true;
            $this->dispatchBrowserEvent('show-delete-modal');
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
            'role' => $userdetails->role,
            'isActive' => $userdetails->isActive,
        ];


        $this->isEditModalOpen = true;
    } else {
        session()->flash('error', 'Product not found.');
    }
}


    public $step = 1;

    
    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function openModal1()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

 
   
    public function updatingSearch(){
        $this->resetPage();
    }

    public function productTypeIDFilter()
    {
        $this->resetPage();
    }

    
public $passwordData = [
    'user_id' => null,
    'new_password' => '',
    'confirm_password' => '',
];

public $isChangePasswordModalOpen = false;

public function openChangePasswordModal($userId)
{
    $this->passwordData['user_id'] = $userId;
    $this->passwordData['new_password'] = '';
    $this->passwordData['confirm_password'] = '';
    $this->isChangePasswordModalOpen = true;
}

public function closeChangePasswordModal()
{
    $this->isChangePasswordModalOpen = false;
    $this->reset('passwordData');
}
public function changePassword()
{
    $this->validate([
        'passwordData.new_password' => 'required|min:8',
        'passwordData.confirm_password' => 'required|same:passwordData.new_password',
    ]);

    try {
        $user = User::find($this->passwordData['user_id']);

        if ($user) {
            $user->update([
                'password' => Hash::make($this->passwordData['new_password']),
            ]);
            session()->flash('message', 'Password changed successfully!');
        } else {
            session()->flash('error', "bobo try again!! own password can't retrieve HAHAH");
        }
    } catch (\Exception $e) {
        session()->flash('error', "bobo try again!! own password can't retrieve HAHAH");
    }

    $this->closeChangePasswordModal();
}
    


 

    public function cancelEdit(){
        $this->reset('editUser');
        $this->isEditModalOpen = false;
    }

    // 
    


    public function getTableQuery(){
        return User::where('isActive', 1);
    }

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editUser'); // Optionally reset the editUser data
    $this->reset('updatedSelectedSerialNumber'); // Reset any other related properties
}
       // Filtering table by brand
  public $filterActivation = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return User::query() // Start with a query builder instance
          ->when($this->filterActivation !== '', function ($query) {
              return $query->where('isActive', $this->filterActivation); // Apply brand filter
          })
          ->when($this->search !== '', function ($query) {
              return $query->where(function ($q) {
                  $q->where('employee_number', 'like', "%{$this->search}%")
                  ->orWhere('lastname', 'like', "%{$this->search}%")
                   ->orWhere('firstname', 'like', "%{$this->search}%")
                    ->orWhere('middlename', 'like', "%{$this->search}%")
                   ->orWhere('role', 'like', "%{$this->search}%");
                
              }); // Apply search filter to multiple fields
          })
          ->get(); // Fetch the records as a collection
  }
  
  // Dynamically calculate the row count based on the current filter
  public function getRowCountProperty()
  {
      return $this->getFilteredRecords()->count(); // Count the filtered records
  }
  
  
public function render()
{
    // Base query to fetch users
    $query = User::query();

    // Apply activation filter if selected
    if ($this->filterActivation !== '') {
        $query->where('isActive', $this->filterActivation);
    } else {
        // Default behavior: Exclude inactive users
        $query->where('isActive', 1);
    }

    // Apply search filter if search term is provided
    if (!empty($this->search)) {
        $query->where(function ($q) {
            $q->where('employee_number', 'like', "%{$this->search}%")
             ->orWhere('lastname', 'like', "%{$this->search}%")
                   ->orWhere('firstname', 'like', "%{$this->search}%")
                    ->orWhere('middlename', 'like', "%{$this->search}%")
              ->orWhere('role', 'like', "%{$this->search}%");
        });
    }

    // Fetch data with pagination
    $records = $query->paginate(10); // Adjust the number of items per page as needed

    // Calculate the total row count after applying filters
    $rowCount = $query->count();

    return view('livewire.add-user-table', [
        'records' => $records, // Paginated data
        'rowCount' => $rowCount, // Total filtered row count
    ]);
}
}

