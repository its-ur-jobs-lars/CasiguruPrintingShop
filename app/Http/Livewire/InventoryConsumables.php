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
           $this->data = [
        'category_id' => null,
        'subcategory_id' => null,
        'remarks' => '',
            'available_rolls' => null,
        // Add other expected fields if needed
    ];
        $this->Category = Category::where('isActive', 1)->get();
        $this->SubCategory = collect(); // empty by default
        $this->Suppliers = supplierInv::where('is_active', 1)->get();
    }

    public function getSelectedSubcategoryNameProperty()
{
    if (empty($this->data['subcategory_id'])) {
        return null;
    }
    $subcategory = SubCategory::find($this->data['subcategory_id']);
    return $subcategory ? $subcategory->subcategory_name : null;
}

public $selectedSubcategoryName = '';


public function updatedDataSubcategoryId($value)
{
     // Filter suppliers based on both category and subcategory
    $this->Suppliers = supplierInv::where('is_active', 1)
        ->where('category_id', $this->data['category_id'] ?? null)
        ->where('subcategory_id', $value)
        ->get();


    $subcategory = SubCategory::find($value);
    $this->selectedSubcategoryName = $subcategory ? $subcategory->subcategory_name : '';

    // Optionally fetch available_rolls when Full Sublimation Printing is selected
    if ($this->selectedSubcategoryName === 'Full Sublimation Printing') {
        $inventory = Inventory::where('subcategory_id', $value)->first();
        $this->data['available_rolls'] = $inventory?->available_rolls ?? 0;
    } else {
        $this->data['available_rolls'] = null; // Clear rolls if not Full Sublimation
    }
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


     $category = Category::find($value);
    if ($category && $category->category_name === 'Full Sublimation Printing') {
        $this->showAutoRemarks = true;
        $itemsPerRoll = rand(50, 100);
        $this->autoRemarks = "{$itemsPerRoll} items/roll";
    } else {
        $this->showAutoRemarks = false;
        $this->autoRemarks = '';
    }
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

    public $autoRemarks = '';
public $showAutoRemarks = false;


    public $isOpen = false;

    public $selectedSubcategory;
    public $availableRolls;

    // Update when subcategory changes
    public function updatedSelectedSubcategory($value)
    {
        $subcategory = SubCategory::find($value);
        $this->selectedSubcategoryName = $subcategory?->subcategory_name ?? '';
    }

    
public function save()
{
    try {
        // Step 1: Validate Input
        $this->validate([
            'data.item_name' => 'required|string|max:255',
            'data.category_id' => 'required|string|max:255',
            'data.subcategory_id' => 'nullable|string|max:255',
            'data.unit' => 'required|string|max:255',
            'data.quantity' => 'nullable|numeric',
            'data.minimum_stock' => 'nullable|numeric',
            'data.purchase_price' => 'nullable|numeric',
            'data.location' => 'nullable|string|max:255',
            'data.supplier_id' => 'nullable|string|max:255',
            'data.expiration_date' => 'nullable|date',
            'data.remarks' => 'nullable|string|max:255',
        ]);

        // Step 2: Extract item name
        $item_name = $this->data['item_name'];

        // Step 3: Generate Next Sequence ID for inventory_id
        $lastInventory = \App\Models\Inventory::where('inventory_id', 'like', "SPL-%-$item_name")
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
            'inventory_id'     => $inventory_id,
            'item_name'        => $item_name,
            'category_id'      => $this->data['category_id'],
            'subcategory_id'   => $this->data['subcategory_id'],
            'unit'             => $this->data['unit'],
            'quantity'         => $this->data['quantity'] ?? 0,
            'available_rolls'  => $this->data['available_rolls'] ?? 0,
            'minimum_stock'    => $this->data['minimum_stock'] ?? 0,
            'purchase_price'   => $this->data['purchase_price'] ?? 0,
            'location'         => $this->data['location'] ?? '',
            'supplier_id'      => $this->data['supplier_id'],
            'expiration_date'  => !empty($this->data['expiration_date']) 
                ? \Carbon\Carbon::parse($this->data['expiration_date'])->format('Y-m-d') 
                : null,
            'remarks'          => $this->data['remarks'] ?? '',
            'added_by'         => \Illuminate\Support\Facades\Auth::user()->username,
        ]);

        // Step 5: Feedback and Reset
        session()->flash('messageInsert', 'Item successfully added.');
        $this->emit('refreshComponent');
        $this->reset(['data']);
        $this->isOpen = false;
        $this->step = 1;

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Validation failed
        session()->flash('errorInsert', 'Validation failed: ' . json_encode($e->errors()));
        \Log::error('Validation Error:', $e->errors());
    } catch (\Exception $e) {
        // General Error
        session()->flash('errorInsert', 'Failed to save item: ' . $e->getMessage());
        \Log::error('Save Error:', ['error' => $e->getMessage()]);
    }
}
}
