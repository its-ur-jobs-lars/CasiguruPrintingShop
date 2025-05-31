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
use Illuminate\Support\Facades\Auth;

class CategoryTable extends Component implements HasTable
{
    use WithPagination, InteractsWithTable {
        WithPagination::resetPage insteadof InteractsWithTable;
    }

    protected $listeners = ['refreshTable' => '$refresh'];

    public $isEditModalOpen = '';
    public $editCat = [
        'id' => null,
        'category_name' => null,
        'description' => null,
        'isActive' => null,
    ];

    public $search = '';
    public $step = 1;
    public $filterActivation = '';


    protected $rules = [
        'editCat.category_name' => 'required|string|max:20',
        'editCat.description' => 'required|string|max:50',
        'editCat.isActive' => 'required|string|max:255'
    ];

    public function getTableQuery(){
        return Category::where('isActive', 1);
    }




    public function confirmDelete($id){
        $this->productToDelete = $id;
        $this->confirmDelete = true;
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    // Edit modal logic
    public function edit($id)
    {
        $CategoryDetail = Category::find($id);

        if ($CategoryDetail) {
            $this->editCat = [
                'id' => $CategoryDetail->id,
                'category_name' => $CategoryDetail->category_name,
                'description' => $CategoryDetail->description,
                'isActive' => $CategoryDetail->isActive,
            ];
            $this->isEditModalOpen = true;
        } else {
            session()->flash('error', 'Category not found.');
        }
    }

    public function update()
    {
        $this->validate();

        $CategoryDetail = Category::find($this->editCat['id']);
        $categoryName = $this->editCat['category_name'];

        if ($CategoryDetail) {
            $CategoryDetail->update([
                'category_name' => $this->editCat['category_name'],
                'description' => $this->editCat['description'],
                'isActive' => $this->editCat['isActive'],
                'updated_by' => Auth::user()->username,
            ]);

                // Emit an event to refresh the table
            $this->emit('refreshTable');

            $this->reset('editCat');
            $this->isEditModalOpen = false;

            session()->flash('messageInsert', 'Category "' . $categoryName . '" updated successfully!');
            $this->dispatchBrowserEvent('closeEditModal');
        } else {
            session()->flash('error', 'Category not found.');
        }
    }

    public function cancelEdit(){
        $this->reset('editCat');
        $this->isEditModalOpen = false;
    }

    public function closeModal1()
    {
        $this->isEditModalOpen = false;
        $this->reset('editCat');
        $this->reset('updatedSelectedSerialNumber');
    }

    public function updatingSearch(){
        $this->resetPage();
    }

    public function productTypeIDFilter()
    {
        $this->resetPage();
    }

    // Filtering table by brand
    protected function getFilteredRecords()
    {
        return Category::query()
            ->when($this->filterActivation !== '', function ($query) {
                return $query->where('isActive', $this->filterActivation);
            })
            ->when($this->search !== '', function ($query) {
                return $query->where(function ($q) {
                    $q->where('category_id', 'like', "%{$this->search}%")
                     ->orWhere('category_name', 'like', "%{$this->search}%")
                      ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->get();
    }

    public function getRowCountProperty()
    {
        return $this->getFilteredRecords()->count();
    }

 public function render()
{
    $records = $this->getFilteredRecords();

    return view('livewire.category-table', [
        'records' => $records,
    ]);
}
}
