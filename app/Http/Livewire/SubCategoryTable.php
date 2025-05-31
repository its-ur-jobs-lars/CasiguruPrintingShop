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
use App\Models\SubCategory;
use App\Models\Category;

class SubCategoryTable extends Component implements HasTable
{
use WithPagination, InteractsWithTable {
        WithPagination::resetPage insteadof InteractsWithTable;
    }

    protected $listeners = ['refreshComponent' => '$refresh'];

    public $isEditModalOpen = false;

    public $editSubCategory = [
        'id' => null,
        'subcategory_name' => null,
        'description' => null,
        'image' => null,
        'isActive' => null,
    ];

    public $search = '';
    public $step = 1;

    protected $updatesQueryString = ['search', 'filterStatus'];

    protected $rules = [
        'editSubCategory.subcategory_name' => 'required|string|max:20',
        'editSubCategory.description' => 'required|string|max:50',
        'editSubCategory.image' => 'required|string|max:50',
        'editSubCategory.isActive' => 'required|string|max:255'
    ];

    public function getTableQuery(){
        return SubCategory::where('isActive', 1);
    }

    // Edit modal logic
    public function edit($id)
    {
        $categorydetails = SubCategory::find($id);

        if ($categorydetails) {
            $this->editSubCategory = [
                'id' => $categorydetails->id,
                'subcategory_name' => $categorydetails->subcategory_name,
                'description' => $categorydetails->description,
                'image' => $categorydetails->image,
                'isActive' => $categorydetails->isActive,
            ];
            $this->isEditModalOpen = true;
        } else {
            session()->flash('error', 'Category not found.');
        }
    }

    public function update()
    {
        $this->validate([
            'editSubCategory.subcategory_name' => 'required|string|max:20',
            'editSubCategory.description' => 'required|string|max:50',
            'editSubCategory.image' => 'required|string|max:50',
           'editSubCategory.isActive' => 'required|string|max:255'
        ]);

        $categorydetails = SubCategory::find($this->editSubCategory['id']);

        if ($categorydetails) {
            $categorydetails->update([
                'subcategory_name' => $this->editSubCategory['subcategory_name'],
                'description' => $this->editSubCategory['description'],
                'image' => $this->editSubCategory['image'],
                'isActive' => $this->editSubCategory['isActive'],
                'updated_by' => Auth::user()->username,
            ]);

                // Emit an event to refresh the table
            $this->emit('refreshTable');

            $this->reset('editSubCategory');
            $this->isEditModalOpen = false;

            session()->flash('messageInsert', 'Category "' . $categoryName . '" updated successfully!');
            $this->dispatchBrowserEvent('closeEditModal');
        } else {
            session()->flash('error', 'Category not found.');
        }
    }

    
     public $Category = [];

    public function mount()
    {
       $this->Category = Category::pluck('category_name', 'category_id')->toArray();
    }

    public function cancelEdit(){
        $this->reset('editSubCategory');
        $this->isEditModalOpen = false;
    }

    public function closeModal1()
    {
        $this->isEditModalOpen = false;
        $this->reset('editSubCategory');
        $this->reset('updatedSelectedSerialNumber');
    }

    public function updatingSearch(){
        $this->resetPage();
    }

    public function productTypeIDFilter()
    {
        $this->resetPage();
    }

    

     public $filterActivation = '';
  // Fetch records and calculate row count dynamically
  protected function getFilteredRecords()
  {
      return SubCategory::query() // Start with a query builder instance
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
    $query = SubCategory::query();

   
    // Apply filterActivation if set
    if ($this->filterActivation !== '') {
        $query->where('isActive', $this->filterActivation);
    }

    // Apply search if set
    if (!empty($this->search)) {
        $query->where(function ($q) {
            $q->where('subcategory_id', 'like', "%{$this->search}%")
              ->orWhere('subcategory_name', 'like', "%{$this->search}%")
                ->orWhere('category_id', 'like', "%{$this->search}%")
              ->orWhere('description', 'like', "%{$this->search}%");
        });
    }

    $records = $query->get();
    $rowCount = $records->count();

    return view('livewire.sub-category-table', [
        'records' => $records,
        'rowCount' => $rowCount,
    ]);
}
}


