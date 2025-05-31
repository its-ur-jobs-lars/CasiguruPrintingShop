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

class PriceListItemTable extends Component implements HasTable
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

    
    
    protected $updatesQueryString = ['search', 'filterStatus'];

 
 
    public $isEditModalOpen = '';
  

    
   //for edit
   public $editPricelist = [
    'id' => null,
    'category_id' => null,
    'subcategory_id' => null,
    'price_10_50' => null,
    'price_51_100' => null,
    'price_101_500' => null,
    'remarks' => null,
    'isActive' => null,
];

//for editing
 protected $rules = [
        'editPricelist.category_id' => 'required|string|max:255',
        'editPricelist.subcategory_id' => 'required|string|max:255',
        'editPricelist.price_10_50' => 'required|string|max:255',
        'editPricelist.price_51_100' => 'required|string|max:50',
        'editPricelist.price_101_500' => 'required|string|max:255',
        'editPricelist.remarks' => 'required|string|max:255',
        'editPricelist.isActive' => 'required|string|max:255'

    ];



//for update
public function update()
{
    $this->validate([ // Apply validation rules
       'editPricelist.category_id' => 'required|string|max:255',
        'editPricelist.subcategory_id' => 'required|string|max:255',
        'editPricelist.price_10_50' => 'required|string|max:255',
        'editPricelist.price_51_100' => 'required|string|max:50',
        'editPricelist.price_101_500' => 'required|string|max:255',
        'editPricelist.remarks' => 'required|string|max:255',
        'editPricelist.isActive' => 'required|string|max:255'
    ]);

    // Find the product by ID
    $employeeDetils = Pricelist::find($this->editPricelist['id']);

    if ($employeeDetils) {
        $employeeDetils->update([
            'category_id' => $this->editPricelist['category_id'],
            'subcategory_id' => $this->editPricelist['subcategory_id'],
            'price_10_50' => $this->editPricelist['price_10_50'],
            'price_51_100' => $this->editPricelist['price_51_100'],
            'price_101_500' => $this->editPricelist['price_101_500'],
            'remarks' => $this->editPricelist['remarks'],
            'isActive' => $this->editPricelist['isActive'],
            'updated_by' => Auth::user()->username,// Store the ID of the user who updated the employee
        ]);

        // Emit an event to refresh the table
        $this->emit('refreshTable');

        // Close the modal
        $this->isEditModalOpen = false;


        // Show a success message
        session()->flash('messageUpdate', 'Pricelist information is updated successfully.');

        // Dispatch a browser event to close the modal
        $this->dispatchBrowserEvent('closeEditModal');
    } else {
        session()->flash('error', 'Product not found.');
    }
}

public function edit($id)
{
    $employeeDetils = Pricelist::find($id);

    if ($employeeDetils) {
        $this->editPricelist = [
            'id' => $employeeDetils->id,
            'category_id' => $employeeDetils->category_id,
            'subcategory_id' => $employeeDetils->subcategory_id,
            'price_10_50' => $employeeDetils->price_10_50,
            'price_51_100' => $employeeDetils->price_51_100,
            'price_101_500' => $employeeDetils->price_101_500,
            'remarks' => $employeeDetils->remarks,
            'isActive' => $employeeDetils->isActive,
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
       $this->Category = Category::pluck('category_name', 'category_id')->toArray();
        $this->SubCategory = SubCategory::pluck('subcategory_name', 'subcategory_id')->toArray();
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
        $this->reset('editPricelist');
        $this->isEditModalOpen = false;
    }

    // 
    


    public function getTableQuery(){
        return Pricelist::where('isActive', 1);
    }

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editPricelist'); // Optionally reset the editPricelist data
  
}
       // Filtering table by brand
  public $filterActivation = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return Pricelist::query() // Start with a query builder instance
          ->when($this->filterActivation !== '', function ($query) {
              return $query->where('isActive', $this->filterActivation); // Apply brand filter
          })
          ->when($this->search !== '', function ($query) {
              return $query->where(function ($q) {
                  $q->where('pricelist_id', 'like', "%{$this->search}%")
                  ->orWhere('category_id', 'like', "%{$this->search}%")
                   ->orWhere('subcategory_id', 'like', "%{$this->search}%");
                
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
    $query = Pricelist::query();

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
            $q->where('pricelist_id', 'like', "%{$this->search}%")
                  ->orWhere('category_id', 'like', "%{$this->search}%")
                   ->orWhere('subcategory_id', 'like', "%{$this->search}%");
        });
    }

      $records = $query->get(); // Get all records without pagination

    // Calculate the total row count after applying filters
    $rowCount = $query->count();

    return view('livewire.price-list-item-table', [
        'records' => $records, // Paginated data
        'rowCount' => $rowCount, // Total filtered row count
    ]);
}
}


