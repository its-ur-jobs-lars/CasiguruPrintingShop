<?php

namespace App\Http\Livewire;


use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Auth;
use App\Models\inventory; // Assuming this is the model for suppliers
use App\Models\supplierInv; // Assuming this is the model for suppliers
use Carbon\Carbon;


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
    
    public function save()
    {
        try {
            $this->validate([
                'data.item_name' => 'required|string|max:255',
                'data.category_id' => 'required|string|max:255',
                'data.subcategory_id' => 'required|string|max:255',
                'data.unit' => 'required|string|max:255',
                'data.quantity' => 'nullable|numeric',
                'data.minimum_stock' => 'nullable|numeric',
                'data.purchase_price' => 'nullable|numeric',
                'data.supplier_id' => 'required|string|max:255',
                'data.expiration_date' => 'nullable|date',
                'data.remarks' => 'nullable|string|max:255',
            ]);

            $item_name = $this->data['item_name'];

            $lastInventory = inventory::where('inventory_id', 'like', "SPL-%-$item_name")
                ->orderByDesc('inventory_id')
                ->first();

            $escapedItemName = preg_quote($item_name, '/');
            if ($lastInventory && preg_match("/SPL-(\d{4})-{$escapedItemName}/", $lastInventory->inventory_id, $matches)) {
                $nextSequence = intval($matches[1]) + 1;
            } else {
                $nextSequence = 1;
            }

            $sequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            $inventory_id = "SPL-{$sequence}-{$item_name}";

            inventory::create([
                'inventory_id' => $inventory_id,
                'item_name' => $item_name,
                'category_id' => $this->data['category_id'],
                'subcategory_id' => $this->data['subcategory_id'],
                'unit' => $this->data['unit'],
                'quantity' => $this->data['quantity'] ?? 0,
                'minimum_stock' => $this->data['minimum_stock'] ?? 0,
                'purchase_price' => $this->data['purchase_price'] ?? 0,
                'selling_price' => $this->data['selling_price'] ?? 0,
                'minimum_stock' => $this->data['minimum_stock'] ?? 0,
                'supplier_id' => $this->data['supplier_id'],
                'expiration_date' => !empty($this->data['expiration_date']) 
                    ? Carbon::parse($this->data['expiration_date'])->format('Y-m-d') 
                    : null,
                'remarks' => $this->data['remarks'] ?? '',
                'added_by' => Auth::user()->username,
            ]);

            $this->emit('refreshComponent');
            $this->reset(['data']);
            $this->isOpen = false;
            $this->step = 1;
            session()->flash('messageInsert', 'Item successfully added!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('errorInsert', 'Validation failed: ' . json_encode($e->errors()));
        } catch (\Exception $e) {
            session()->flash('errorInsert', 'Failed to save item: ' . $e->getMessage());
        }
    }
   
}
