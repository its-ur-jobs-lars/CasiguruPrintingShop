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
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Pricelist;
use Illuminate\Support\Facades\Auth;


class CategoryTable extends Component implements HasTable
{
    use WithPagination, InteractsWithTable {
        WithPagination::resetPage insteadof InteractsWithTable;
    }
    protected $listeners = ['refreshComponent' => '$refresh'];

    
    public $search = '';

    
    
    protected $updatesQueryString = ['search', 'filterStatus'];

 
 
    public $isEditModalOpen = '';
  

    
   //for edit
   public $editCategory = [
    'id' => null,
    'category_name' => null,
    'description' => null,
    'isActive' => null,
];

//for editing
 protected $rules = [
        'editCategory.category_name' => 'required|string|max:255',
        'editCategory.description' => 'required|string|max:50',
        'editCategory.isActive' => 'required|string|max:255'

    ];



//for update
public function update()
{
    $this->validate([ // Apply validation rules
         'editCategory.category_name' => 'required|string|max:255',
        'editCategory.description' => 'required|string|max:50',
        'editCategory.isActive' => 'required|string|max:255'
    ]);

    // Find the product by ID
    $categoryDetails = SubCategory::find($this->editCategory['id']);

    if ($categoryDetails) {
        $categoryDetails->update([
            'category_name' => $this->editCategory['category_name'],
            'description' => $this->editCategory['description'],
            'isActive' => $this->editCategory['isActive'],
            'updated_by' => Auth::user()->username,// Store the ID of the user who updated the employee
        ]);

        // Emit an event to refresh the table
        $this->emit('refreshTable');

        // Close the modal
        $this->isEditModalOpen = false;


        // Show a success message
        session()->flash('messageUpdate', 'SubCategory information is updated successfully.');

        // Dispatch a browser event to close the modal
        $this->dispatchBrowserEvent('closeEditModal');
    } else {
        session()->flash('error', 'Product not found.');
    }
}

public function edit($id)
{
    $categoryDetails = Category::find($id);

    if ($categoryDetails) {
        $this->editCategory = [
            'id' => $categoryDetails->id,
            'category_name' => $categoryDetails->category_name,
            'description' => $categoryDetails->description,
            'isActive' => $categoryDetails->isActive,
        ];

        $this->isEditModalOpen = true;
    } else {
        session()->flash('error', 'Employee not found.');
    }
}

   public $Category = [];
   public $SubCategory = [];

    public function mount()
    {
       $this->Category = Category::pluck('category_name', 'category_name')->toArray();
        $this->SubCategory = SubCategory::pluck('subcategory_name', 'subcategory_name')->toArray();
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
        $this->reset('editCategory');
        $this->isEditModalOpen = false;
    }

    // 
    


    public function getTableQuery(){
        return Category::where('isActive', 1);
    }

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editCategory'); // Optionally reset the editCategory data
  
}
       // Filtering table by brand
  public $filterActivation = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return Category::where('isActive', 1) // Start with a query builder instance
          ->when($this->filterActivation !== '', function ($query) {
              return $query->where('isActive', $this->filterActivation); // Apply brand filter
          })
          ->when($this->search !== '', function ($query) {
              return $query->where(function ($q) {
                  $q->where('category_id', 'like', "%{$this->search}%")
                        ->orWhere('category_name', 'like', "%{$this->search}%");
                    
                
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
    $query = Category::query();

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
           $q->where('category_id', 'like', "%{$this->search}%")
                        ->orWhere('category_name', 'like', "%{$this->search}%");
        });
    }

      $records = $query->get(); // Get all records without pagination

    // Calculate the total row count after applying filters
    $rowCount = $query->count();

    return view('livewire.category-table', [
        'records' => $records, // Paginated data
        'rowCount' => $rowCount, // Total filtered row count
    ]);
}
}



