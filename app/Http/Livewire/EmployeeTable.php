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
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;


class EmployeeTable extends Component implements HasTable
{
    

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
        $this->editEmployee['serial_number'] = 'Not Available';
    }
    }

      public function confirmDelete($id){
            $this->productToDelete = $id;
            $this->confirmDelete = true;
            $this->dispatchBrowserEvent('show-delete-modal');
    }

    
   //for edit
   public $editEmployee = [
    'id' => null,
    'employee_id' => null,
    'emp_Lastname' => null,
    'emp_Firstname' => null,
    'emp_Middlename' => null,
    'ext_name' => null,
    'task' => null,
    'date_Hired' => null,
    'description' => null,
    'employee_number' => null,
    'isActive' => null,
];

//for editing
 protected $rules = [
        'editEmployee.emp_Lastname' => 'required|string|max:255',
        'editEmployee.emp_Firstname' => 'required|string|max:255',
        'editEmployee.emp_Middlename' => 'required|string|max:255',
        'editEmployee.ext_name' => 'required|string|max:50',
        'editEmployee.task' => 'required|string|max:255',
        'editEmployee.date_Hired' => 'required|string|max:255',
        'editEmployee.description' => 'required|string|max:255',
        'editEmployee.employee_number' => 'required|string|max:255',
        'editEmployee.isActive' => 'required|string|max:255'

    ];



//for update
public function update()
{
    $this->validate([ // Apply validation rules
        'editEmployee.emp_Lastname' => 'required|string|max:255',
        'editEmployee.emp_Firstname' => 'required|string|max:255',
        'editEmployee.emp_Middlename' => 'required|string|max:255',
        'editEmployee.ext_name' => 'required|string|max:50',
        'editEmployee.task' => 'required|string|max:255',
        'editEmployee.date_Hired' => 'required|string|max:255',
        'editEmployee.description' => 'required|string|max:255',
        'editEmployee.employee_number' => 'required|string|max:255',
        'editEmployee.isActive' => 'required|string|max:255'
    ]);

    // Find the product by ID
    $employeeDetils = Employee::find($this->editEmployee['id']);

    if ($employeeDetils) {
        $employeeDetils->update([
            'emp_Lastname' => $this->editEmployee['emp_Lastname'],
            'emp_Firstname' => $this->editEmployee['emp_Firstname'],
            'emp_Middlename' => $this->editEmployee['emp_Middlename'],
            'ext_name' => $this->editEmployee['ext_name'],
            'task' => $this->editEmployee['task'],
            'date_Hired' => $this->editEmployee['date_Hired'],
            'description' => $this->editEmployee['description'],
            'employee_number' => $this->editEmployee['employee_number'],
            'isActive' => $this->editEmployee['isActive'],
            'updated_by' => Auth::user()->username,// Store the ID of the user who updated the employee
        ]);

        // Emit an event to refresh the table
        $this->emit('refreshTable');

        // Close the modal
        $this->isEditModalOpen = false;


        // Show a success message
        session()->flash('messageUpdate', 'Employee information is updated successfully.');

        // Dispatch a browser event to close the modal
        $this->dispatchBrowserEvent('closeEditModal');
    } else {
        session()->flash('error', 'Product not found.');
    }
}

public function edit($id)
{
    $employeeDetils = Employee::find($id);

    if ($employeeDetils) {
        $this->editEmployee = [
            'id' => $employeeDetils->id,
            'emp_Lastname' => $employeeDetils->emp_Lastname,
            'emp_Firstname' => $employeeDetils->emp_Firstname,
            'emp_Middlename' => $employeeDetils->emp_Middlename,
            'ext_name' => $employeeDetils->ext_name,
            'task' => $employeeDetils->task,
            'date_Hired' => $employeeDetils->date_Hired,
            'description' => $employeeDetils->description,
            'employee_number' => $employeeDetils->employee_number,
            'isActive' => $employeeDetils->isActive,
        ];

        $this->isEditModalOpen = true;
    } else {
        session()->flash('error', 'Employee not found.');
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

    
    public function cancelEdit(){
        $this->reset('editEmployee');
        $this->isEditModalOpen = false;
    }

    // 
    


    public function getTableQuery(){
        return Employee::where('isActive', 1);
    }

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editEmployee'); // Optionally reset the editEmployee data
  
}
       // Filtering table by brand
  public $filterActivation = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return Employee::query() // Start with a query builder instance
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
    $query = Employee::query();

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

      $records = $query->get(); // Get all records without pagination

    // Calculate the total row count after applying filters
    $rowCount = $query->count();

    return view('livewire.employee-table', [
        'records' => $records, // Paginated data
        'rowCount' => $rowCount, // Total filtered row count
    ]);
}
}

