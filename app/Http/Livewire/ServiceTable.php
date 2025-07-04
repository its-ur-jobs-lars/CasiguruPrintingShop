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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;




class ServiceTable extends Component  implements HasTable
{
  

    use WithPagination, InteractsWithTable {
        WithPagination::resetPage insteadof InteractsWithTable;
    }

    protected $listeners = ['refreshComponent' => '$refresh'];

    
    public $search = '';

    
    
    protected $updatesQueryString = ['search', 'filterStatus'];

 
 
    public $isEditModalOpen = '';
  

    
   //for edit
   public $editServices = [
    'id' => null,
    'category_id' => null,
    'subcategory_id' => null,
    'subcategory_name' => null,
    'description' => null,
    'image' => null,
    'existing_image' => '', // will store the path of the image in storage
    'isActive' => null,
];

//for editing
 protected $rules = [
        'editServices.category_id' => 'required|string|max:255',
        'editServices.subcategory_id' => 'required|string|max:255',
        'editServices.subcategory_name' => 'required|string|max:255',
        'editServices.description' => 'required|string|max:50',
        'editServices.image' => 'required|string|max:255',
        'editServices.isActive' => 'required|boolean'

    ];


public function update()
{   
    try{
    $this->validate([
        'editServices.category_id' => 'required|string|max:255',
        'editServices.subcategory_id' => 'required|string|max:255',
        'editServices.subcategory_name' => 'required|string|max:255',
        'editServices.description' => 'required|string|max:50',
        'editServices.image' => 'nullable|image|max:2048',
        'editServices.isActive' => 'required|boolean'
    ]);

    $subCategory = SubCategory::find($this->editServices['id']);

    if (!$subCategory) {
        session()->flash('error', 'SubCategory not found.');
        return;
    }

    // Handle image upload
    $imagePath = $subCategory->image; // default to existing image

   if ($this->editServices['image'] instanceof \Livewire\TemporaryUploadedFile) {
    // Delete old image
    if ($subCategory->image && Storage::exists('public/' . $subCategory->image)) {
        Storage::delete('public/' . $subCategory->image);
    }

    // Get the original extension (e.g., jpg, png)
    $extension = $this->editServices['image']->getClientOriginalExtension();

    // Sanitize the filename
    $fileName = Str::slug($this->editServices['subcategory_name']) . '.' . $extension;

    // Store the file with a custom name
    $imagePath = $this->editServices['image']->storeAs('images/products', $fileName, 'public');
}

    

    // Update SubCategory
    $subCategory->update([
        'category_id' => $this->editServices['category_id'],
        'subcategory_id' => $this->editServices['subcategory_id'],
        'subcategory_name' => $this->editServices['subcategory_name'],
        'description' => $this->editServices['description'],
        'image' => $imagePath,
        'isActive' => $this->editServices['isActive'],
        'updated_by' => Auth::user()->username,
    ]);

    // Optional: update the related service if needed
    if (isset($this->editServices['name'])) {
        $service = Service::find($this->editServices['id']);
        if ($service) {
            $service->name = $this->editServices['name'];
            $service->image = $imagePath;
            $service->save();
        }
    }

    $this->emit('refreshTable');
    $this->isEditModalOpen = false;
    session()->flash('messageUpdate', 'SubCategory information updated successfully.');
    $this->dispatchBrowserEvent('closeEditModal');
   } catch (\Illuminate\Validation\ValidationException $e) {
    session()->flash('errorInsert', json_encode($e->errors()));
} catch (\Exception $e) {
    session()->flash('errorInsert', $e->getMessage());
}
}

public function edit($id)
{
    $employeeDetils = SubCategory::find($id);

    if ($employeeDetils) {
        $this->editServices = [
            'id' => $employeeDetils->id,
            'category_id' => $employeeDetils->category_id,
            'subcategory_id' => $employeeDetils->subcategory_id,
            'subcategory_name' => $employeeDetils->subcategory_name,
            'description' => $employeeDetils->description,
              'image' => null, // Clear image input
            'existing_image' => $employeeDetils->image, // This is the important line
            'isActive' => $employeeDetils->isActive,
        ];

        $this->isEditModalOpen = true;
    } else {
        session()->flash('error', 'Employee not found.');
    }
}

   public $Category = [];
   public $SubCategory = [];

    public function mount($serviceId = null)
{
    // Load dropdowns
    $this->Category = Category::pluck('category_name', 'category_id')->toArray();
    $this->SubCategory = SubCategory::pluck('subcategory_name', 'subcategory_id')->toArray();

    // If editing existing service
    if ($serviceId) {
        $service = Service::findOrFail($serviceId);
        $this->editServices = [
            'id' => $service->id,
            'name' => $service->name,
            'image' => null,
            'existing_image' => $service->image,
        ];
    } else {
        // If creating new service
        $this->editServices = [
            'id' => null,
            'name' => '',
            'image' => null,
            'existing_image' => null,
        ];
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
        $this->reset('editServices');
        $this->isEditModalOpen = false;
    }

    // 
    


    public function getTableQuery(){
        return SubCategory::where('isActive', 1);
    }

    //for closing the edit modal
    public function closeModal1()
{
    $this->isEditModalOpen = false; // Close the modal
    $this->reset('editServices'); // Optionally reset the editServices data
  
}
       // Filtering table by brand
  public $filterActivation = ''; // Default value for the dropdown filter
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return SubCategory::where('isActive', 1) // Start with a query builder instance
          ->when($this->filterActivation !== '', function ($query) {
              return $query->where('isActive', $this->filterActivation); // Apply brand filter
          })
          ->when($this->search !== '', function ($query) {
              return $query->where(function ($q) {
                  $q->where('subcategory_name', 'like', "%{$this->search}%")
                        ->orWhere('subcategory_id', 'like', "%{$this->search}%")
                        ->orWhere('category_id', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                
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
    $query = SubCategory::query();

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
             $q->where('subcategory_name', 'like', "%{$this->search}%")
                        ->orWhere('subcategory_id', 'like', "%{$this->search}%")
                        ->orWhere('category_id', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
        });
    }

      $records = $query->get(); // Get all records without pagination

    // Calculate the total row count after applying filters
    $rowCount = $query->count();

    return view('livewire.service-table', [
        'records' => $records, // Paginated data
        'rowCount' => $rowCount, // Total filtered row count
    ]);
}
}



