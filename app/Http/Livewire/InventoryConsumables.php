<?php

namespace App\Http\Livewire;


use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Auth;
use App\Models\supplierInv; // Assuming this is the model for suppliers

class InventoryConsumables extends Component
{
    public $data = [];
    public function mount()
    {
        $this->Category = Category::where('isActive', 1)->get();
        $this->SubCategory = collect(); // empty by default
        $this->Suppliers = supplierInv::where('is_active', 1)->get();
    }

   public function updatedDataCategoryId($value)
{
    // Filter subcategories
    $this->SubCategory = SubCategory::where('category_id', $value)
        ->where('isActive', 1)
        ->get();

    // Reset subcategory
    $this->data['subcategory_id'] = null;
    $this->Suppliers = collect(); // clear suppliers until subcategory is selected
}

public function updatedDataSubcategoryId($value)
{
    // Filter suppliers based on both category and subcategory
    $this->Suppliers = supplierInv::where('is_active', 1)
        ->where('category_id', $this->data['category_id'] ?? null)
        ->where('subcategory_id', $value)
        ->get();
}

    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
    public function previousStep1() { $this->step = 2; }

    public function render()
    {
        return view('livewire.inventory-consumables');
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->step = 1;
    }

    public $isOpen = false;
    
   
   
}
