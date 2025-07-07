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
use App\Models\supplierInv;
use Illuminate\Support\Facades\Auth;

class SupplierTable extends Component  implements HasTable
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
   public $editSupplier = [
    'id' => null,
    'name' => null,
    'category_id' => null,
    'subcategory_id' => null,
    'contact_person' => null,
    'contact_number' => null,
    'address' => null,
    'remarks' => null,
    'is_active' => null,

      
];

//for editing
 public $rules = [
        'editSupplier.name' => 'required|string|max:255',
        'editSupplier.category_id' => 'required|string|max:255',
        'editSupplier.subcategory_id' => 'required|string|max:255',
        'editSupplier.contact_person' => 'required|string|max:255',
        'editSupplier.contact_number' => 'required|string|max:255',
        'editSupplier.address' => 'required|string|max:255',
        'editSupplier.remarks' => 'required|string|max:255',
        'editSupplier.is_active' => 'required|string|max:255'

    ];



public function update()
{
    try {
        $this->validate([
            'editSupplier.name' => 'required|string|max:255',
            'editSupplier.category_id' => 'required|string|max:255',
            'editSupplier.subcategory_id' => 'required|string|max:255',
            'editSupplier.contact_person' => 'required|string|max:255',
            'editSupplier.contact_number' => 'required|string|max:255',
            'editSupplier.address' => 'required|string|max:255',
            'editSupplier.remarks' => 'required|string|max:255',
            'editSupplier.is_active' => 'required|string|max:255'
        ]);

        $supplierDetails = supplierInv::find($this->editSupplier['id']);


        if ($supplierDetails) {
            $supplierDetails->update([
                'name' => $this->editSupplier['name'],
                'category_id' => $this->editSupplier['category_id'],
                'subcategory_id' => $this->editSupplier['subcategory_id'],
                'contact_person' => $this->editSupplier['contact_person'],
                'contact_number' => $this->editSupplier['contact_number'],
                'address' => $this->editSupplier['address'],
                'remarks' => $this->editSupplier['remarks'],
                'is_active' => $this->editSupplier['is_active'],
                'updated_by' => Auth::user()->username ?? 'System',
            ]);

            $this->emit('refreshTable');
            $this->isEditModalOpen = false;
            $this->dispatchBrowserEvent('closeEditModal');
            session()->flash('messageUpdate', 'Supplier information updated successfully.');
        } else {
            session()->flash('error', 'Supplier not found.');
        }
    }catch (\Exception $e) {
    \Log::error('Update Supplier Error', ['error' => $e->getMessage()]);
    session()->flash('UpdateError', 'Failed to update supplier: ' . $e->getMessage());
}
}



 public function edit($id)
{
    $supplier = supplierInv::find($id);

    if ($supplier) {
        $this->editSupplier = [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'category_id' => $supplier->category_id,
            'subcategory_id' => $supplier->subcategory_id,
            'contact_person' => $supplier->contact_person,
            'contact_number' => $supplier->contact_number,
            'address' => $supplier->address,
            'remarks' => $supplier->remarks,
            'is_active' => $supplier->is_active,
        ];

        // Load category and subcategory for dropdowns
        $this->Category = Category::all(); // for select option using ->id and ->category_name
        $this->SubCategory = SubCategory::where('category_id', $supplier->category_id)->get();

        $this->isEditModalOpen = true;
    } else {
        session()->flash('error', 'Supplier not found.');
    }
}


   


   public $category = [];
   public $subcategory = [];

    public function mount()
    {
       $this->category = Category::pluck('category_name', 'category_id')->toArray();
        $this->subcategory = SubCategory::pluck('subcategory_name', 'subcategory_id')->toArray();
        
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
        $this->reset('editSupplier');
        $this->isEditModalOpen = false;
    }

    // 
    


    public function getTableQuery(){
        return supplierInv::where('is_active', 1);
    }

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editSupplier'); // Optionally reset the editSupplier data
  
}
       // Filtering table by brand
  public $filterActivation = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return supplierInv::where('is_active', 1) // Start with a query builder instance
          ->when($this->filterActivation !== '', function ($query) {
              return $query->where('is_active', $this->filterActivation); // Apply brand filter
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
    $query = supplierInv::query();

    // Apply activation filter if selected
    if ($this->filterActivation !== '') {
        $query->where('is_active', $this->filterActivation);
    } else {
        // Default behavior: Exclude inactive users
        $query->where('is_active', 1);
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

    return view('livewire.supplier-table', [
        'records' => $records, // Paginated data
        'rowCount' => $rowCount, // Total filtered row count
    ]);
}
}



