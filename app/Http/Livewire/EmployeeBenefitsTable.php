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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\employee_benefits;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class EmployeeBenefitsTable extends Component implements HasTable   
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
        $this->editBenefit['serial_number'] = 'Not Available';
    }
    }

      public function confirmDelete($id){
            $this->productToDelete = $id;
            $this->confirmDelete = true;
            $this->dispatchBrowserEvent('show-delete-modal');
    }

    
   //for edit
   public $editBenefit = [
    'id' => null,
    'employee_number' => null,
    'date' => null,
    'employee_name' => null,
    'sss' => null,
    'sss_number' => null,
    'pagibig' => null,
    'pagibig_number' => null,
    'philhealth' => null,
    'philhealth_number' => null,
    'updated_by' => null,
    'isActive' => null,
];

//for editing
 protected $rules = [
        'editBenefit.employee_number' => 'required|string|max:20',
        'editBenefit.date' => 'required|date|max:255',
        'editBenefit.employee_name' => 'required|string|max:50',
        'editBenefit.sss' => 'required|string|max:50',
        'editBenefit.sss_number' => 'required|string|max:255',
        'editBenefit.pagibig' => 'required|string|max:255',
        'editBenefit.pagibig_number' => 'required|string|max:255',
        'editBenefit.philhealth' => 'required|string|max:255',
        'editBenefit.philhealth_number' => 'required|string|max:255',
         'editBenefit.isActive' => 'required|in:0,1',

    ];

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



//for update
public function update()
{
    $this->validate([ // Apply validation rules
        'editBenefit.employee_number' => 'required|string|max:20',
        'editBenefit.date' => 'required|date|max:255',
        'editBenefit.employee_name' => 'required|string|max:50',
        'editBenefit.sss' => 'required|string|max:50',
        'editBenefit.sss_number' => 'required|string|max:255',
        'editBenefit.pagibig' => 'required|string|max:255',
        'editBenefit.pagibig_number' => 'required|string|max:255',
        'editBenefit.philhealth' => 'required|string|max:255',
        'editBenefit.philhealth_number' => 'required|string|max:255',
         'editBenefit.isActive' => 'required|in:0,1',
    ]);

    // Find the product by ID
    $userdetails = employee_benefits::find($this->editBenefit['id']);

    if ($userdetails) {
        $userdetails->update([
            'employee_number' => $this->editBenefit['employee_number'],
            'date' => $this->editBenefit['date'],
            'employee_name' => $this->editBenefit['employee_name'],
            'sss' => $this->editBenefit['sss'],
            'sss_number' => $this->editBenefit['sss_number'],
            'pagibig' => $this->editBenefit['pagibig'],
            'pagibig_number' => $this->editBenefit['pagibig_number'],
            'philhealth' => $this->editBenefit['philhealth'],   
            'philhealth_number' => $this->editBenefit['philhealth_number'],
            'updated_by' => Auth::user()->username,
            'isActive' => $this->editBenefit['isActive']
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
    $userdetails = employee_benefits::find($id);

    if ($userdetails) {
        $this->editBenefit = [
            'id' => $userdetails->id, // Include the product ID
            'employee_number' => $userdetails->employee_number,
            'date' => $userdetails->date,   
            'employee_name' => $userdetails->employee_name,
            'sss' => $userdetails->sss,
            'sss_number' => $userdetails->sss_number,
            'pagibig' => $userdetails->pagibig,
            'pagibig_number' => $userdetails->pagibig_number,
            'philhealth' => $userdetails->philhealth,
            'philhealth_number' => $userdetails->philhealth_number,
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



 

    public function cancelEdit(){
        $this->reset('editBenefit');
        $this->isEditModalOpen = false;
    }

    //  public function unblockUser($employeeNumber)
    // {
    //     if (auth()->user()->admin != 1) {
    //         session()->flash('error', 'Unauthorized action.');
    //         return;
    //     }

    //     $key = Str::lower('login:' . $employeeNumber);
    //     RateLimiter::clear($key);

    //     session()->flash('messageUpdate', 'User login attempts have been reset.');
    // }


    public function getTableQuery(){
        return User::where('isActive', 1);
    }

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editBenefit'); // Optionally reset the editBenefit data
    $this->reset('updatedSelectedSerialNumber'); // Reset any other related properties
}
       // Filtering table by brand
  public $filterActivation = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return employee_benefits::where('isActive', 1) // Start with a query builder instance
          ->when($this->filterActivation !== '', function ($query) {
              return $query->where('isActive', $this->filterActivation); // Apply brand filter
          })
          ->when($this->search !== '', function ($query) {
              return $query->where(function ($q) {
                  $q->where('employee_number', 'like', "%{$this->search}%")
                  ->orWhere('lastname', 'like', "%{$this->search}%")
                   ->orWhere('firstname', 'like', "%{$this->search}%")
                    ->orWhere('middlename', 'like', "%{$this->search}%")
                    ->orwhere('username', 'like', "%{$this->search}%")
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
    $query = employee_benefits::query();

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
                    ->orwhere('username', 'like', "%{$this->search}%")
              ->orWhere('role', 'like', "%{$this->search}%");
        });
    }

      $records = $query->get(); // Get all records without pagination

    // Calculate the total row count after applying filters
    $rowCount = $query->count();

    return view('livewire.employee-benefits-table', [
        'records' => $records, // Paginated data
        'rowCount' => $rowCount, // Total filtered row count
    ]);
}
}

