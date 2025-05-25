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
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeActivityLogs;

class EmployeeLogsTable extends Component implements HasTable
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


    public function getTableQuery()
{
    return EmployeeActivityLogs::query();
}

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editUser'); // Optionally reset the editUser data
    $this->reset('updatedSelectedSerialNumber'); // Reset any other related properties
}
       // Filtering table by brand
  public $filterAction = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return EmployeeActivityLogs::query() // Start with a query builder instance
          ->when($this->filterAction !== '', function ($query) {
              return $query->where('action', $this->filterAction); // Apply brand filter
          })
          ->when($this->search !== '', function ($query) {
              return $query->where(function ($q) {
                  $q->where('user_id', 'like', "%{$this->search}%")
                  ->orWhere('user_name', 'like', "%{$this->search}%");
                
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
    // Build the query with filters and search
    $query = EmployeeActivityLogs::query()
        ->when($this->filterAction !== '', function ($query) {
            return $query->where('action', $this->filterAction);
        })
        ->when($this->search !== '', function ($query) {
            return $query->where(function ($q) {
                $q->where('user_id', 'like', "%{$this->search}%")
                  ->orWhere('user_name', 'like', "%{$this->search}%");
            });
        });

    // Fetch data with pagination
    $records = $query->paginate(10);

    // Get the total row count after applying filters
    $rowCount = $records->total();

    return view('livewire.employee-logs-table', [
        'records' => $records,
        'rowCount' => $rowCount,
    ]);
}
}


