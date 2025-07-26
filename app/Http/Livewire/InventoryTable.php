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
use App\Models\inventory;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class InventoryTable extends Component implements HasTable
{
   
    use WithPagination, InteractsWithTable {
        WithPagination::resetPage insteadof InteractsWithTable;
    }

//     public function showThread()
// {
//     $this->showPaymentThread = true;
//     $this->loadThreadWithInventoryCheck();
// }

public $showThreadPayment = false; // purely for visibility flag
public $threadPaymentData = []; // holds the actual data


public function closeThread()
{
    $this->showPaymentThread = false;
}

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function nextStep(){
        $this->step = 2;
    }

     public function nextStep1(){
        $this->step = 3;
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

    public $hasLowStock = false;
     public $showPaymentThread = false;

    public function checkLowStockStatus()
{
    $this->hasLowStock = inventory::whereColumn('quantity', '<', 'minimum_stock')->exists();
}

public function loadThreadWithInventoryCheck()
{
    // Always trigger the modal
    $this->showPaymentThread = true;

    // Check for low stock items
    $lowStockItems = inventory::with(['subcategory.category'])
        ->whereColumn('quantity', '<', 'minimum_stock')
        ->get();

    // If no low stock items, just show an empty array to display "No low stock" message
    if ($lowStockItems->isEmpty()) {
        $this->showThreadPayment = [];
        return;
    }

    // Otherwise, group and format the results
    $grouped = $lowStockItems->groupBy(function ($item) {
        return $item->updated_at->toDateString();
    });

    $this->showThreadPayment = $grouped->map(function ($group, $date) {
        return [
            'date' => $date,
            'items' => $group->map(function ($item) {
                return [
                    'label' => $item->subcategory->category->category_name ?? 'N/A',
                    'subcategory_name' => $item->subcategory->subcategory_name ?? 'N/A',
                    'quantity' => $item->quantity,
                    'minimum_stock' => $item->minimum_stock,
                    'status' => $item->quantity == 0 ? 'Out of Stock' : 'Low Stock',
                ];
            }),
        ];
    })->values()->all();
}



   //for edit
   public $editInventory = [
    'id' => null,
    'item_name' => null,
    'category_id' => null,
    'subcategory_id' => null,
    'unit' => null,
    'quantity' => null,
    'minimum_stock' => null,
    'purchase_price' => null,
    'available_rolls' => null,
    'supplier_id' => null,
    'location' => null,
    'remarks' => null,
    'isActive' => null,

      
];

//for editing
 public $rules = [
        'editInventory.item_name' => 'required|string|max:255',
        'editInventory.category_id' => 'required|string|max:255',
        'editInventory.subcategory_id' => 'required|string|max:255',
        'editInventory.unit' => 'required|string|max:255',
        'editInventory.quantity' => 'required|string|max:255',
        'editInventory.minimum_stock' => 'required|string|max:255',
         'editInventory.purchase_price' => 'required|string|max:255',
        'editInventory.available_rolls' => 'required|string|max:255',
        'editInventory.supplier_id' => 'required|string|max:255',
        'editInventory.location' => 'required|string|max:255',
        'editInventory.remarks' => 'required|string|max:255',
        'editInventory.isActive' =>  'required|boolean',

    ];



public function update()
{
    try {
        $this->validate([
    'editInventory.item_name' => 'required|string|max:255',
    'editInventory.category_id' => 'required|string|max:255',
    'editInventory.subcategory_id' => 'required|string|max:255',
    'editInventory.unit' => 'required|string|max:255',
    'editInventory.quantity' => 'required|numeric',
    'editInventory.minimum_stock' => 'required|numeric',
    'editInventory.purchase_price' => 'required|numeric',
    'editInventory.available_rolls' => 'required|numeric',
    'editInventory.supplier_id' => 'required|string|max:255',
    'editInventory.location' => 'required|string|max:255',
    'editInventory.remarks' => 'required|string|max:255',
    'editInventory.isActive' => 'required|boolean',
]);

        $supplierDetails = inventory::find($this->editInventory['id']);


        if ($supplierDetails) {
            $supplierDetails->update([
                'item_name' => $this->editInventory['item_name'],
                'category_id' => $this->editInventory['category_id'],
                'subcategory_id' => $this->editInventory['subcategory_id'],
                'unit' => $this->editInventory['unit'],
                'quantity' => $this->editInventory['quantity'],
                'minimum_stock' => $this->editInventory['minimum_stock'],
                'purchase_price' => $this->editInventory['purchase_price'],
                'available_rolls' => $this->editInventory['available_rolls'],
                'supplier_id' => $this->editInventory['supplier_id'],
                'location' => $this->editInventory['location'],
                'remarks' => $this->editInventory['remarks'],
                'isActive' => $this->editInventory['isActive'],
                'updated_by' => Auth::user()->id, // Assuming you want to track who
                
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
    $supplier = inventory::find($id);

    if ($supplier) {
        $this->editInventory = [
            'id' => $supplier->id,
            'item_name' => $supplier->item_name,
            'category_id' => $supplier->category_id,
            'subcategory_id' => $supplier->subcategory_id,
            'unit' => $supplier->unit,
            'quantity' => $supplier->quantity,
            'minimum_stock' => $supplier->minimum_stock,
            'purchase_price' => $supplier->purchase_price,
            'available_rolls' => $supplier->available_rolls,
            'supplier_id' => $supplier->supplier_id,
            'location' => $supplier->location,
            'remarks' => $supplier->remarks,
            'isActive' => $supplier->isActive
            
        ];

        // Load category and subcategory for dropdowns
        $this->Category = Category::all(); // for select option using ->id and ->category_name
        $this->SubCategory = SubCategory::where('category_id', $supplier->category_id)->get();
         $this->Supplier = supplierInv::where('category_id', $supplier->category_id)->get();

        $this->isEditModalOpen = true;
    } else {
        session()->flash('error', 'Supplier not found.');
    }
}


   


   public $category = [];
   public $subcategory = [];
   public $supplier = [];

  

    public function mount()
    {
        $this->checkLowStockStatus();
        $this->loadThreadWithInventoryCheck();
       $this->category = Category::pluck('category_name', 'category_id')->toArray();
        $this->subcategory = SubCategory::pluck('subcategory_name', 'subcategory_id')->toArray();
          $this->supplier = supplierInv::pluck('name', 'supplier_id')->toArray();
        
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
        $this->reset('editInventory');
        $this->isEditModalOpen = false;
    }

    // 
    


    public function getTableQuery(){
        return inventory::where('isActive', 1);
    }

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editInventory'); // Optionally reset the editInventory data
  
}
       // Filtering table by brand
  public $filterActivation = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return inventory::where('isActive', 1) // Start with a query builder instance
          ->when($this->filterActivation !== '', function ($query) {
              return $query->where('isActive', $this->filterActivation); // Apply brand filter
          })
          ->when($this->search !== '', function ($query) {
              return $query->where(function ($q) {
                  $q->where('item_name', 'like', "%{$this->search}%")
                  ->orWhere('unit', 'like', "%{$this->search}%")
                   ->orWhere('location', 'like', "%{$this->search}%");
                
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
    $query = inventory::query();

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
            $q->where('item_name', 'like', "%{$this->search}%")
                  ->orWhere('unit', 'like', "%{$this->search}%")
                   ->orWhere('location', 'like', "%{$this->search}%");
        });
    }

      $records = $query->get(); // Get all records without pagination

    // Calculate the total row count after applying filters
    $rowCount = $query->count();

    return view('livewire.inventory-table', [
        'records' => $records, // Paginated data
        'rowCount' => $rowCount, // Total filtered row count
    ]);
}
}




