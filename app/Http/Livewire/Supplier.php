<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\supplierInv;
use Illuminate\Support\Facades\Auth;

class Supplier extends Component
{
    public $data = [];
    public $isOpen = false;
    public $Category;
    public $SubCategory;
    public $step = 1;

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

    public function render()
    {
        return view('livewire.supplier');
    }

    public function mount()
    {
        $this->Category = Category::where('isActive', 1)->get();
        $this->SubCategory = collect(); // empty by default
    }

    public function updatedDataCategoryId($value)
    {
        $this->SubCategory = SubCategory::where('category_id', $value)
            ->where('isActive', 1)
            ->get();

        // Reset subcategory_id if it’s not in filtered list
        if (!$this->SubCategory->contains('subcategory_id', $this->data['subcategory_id'] ?? null)) {
            $this->data['subcategory_id'] = null;
        }
    }

    public function nextStep() { $this->step = 2; }
    public function previousStep() { $this->step = 1; }
    public function nextStep1() { $this->step = 3; }
    public function previousStep1() { $this->step = 2; }

    public function save()
    {
        try {
            $this->validate([
                'data.name' => 'required',
                'data.category_id' => 'required|exists:category,category_id',
                'data.subcategory_id' => 'required|exists:sub-category,subcategory_id',
                'data.contact_person' => 'required|string|max:255',
                'data.contact_number' => 'nullable|string|max:255',
                'data.address' => 'nullable|string|max:255',
                'data.remarks' => 'nullable|string|max:255',
            ]);

            $categoryId = $this->data['category_id'];

            $lastSupplier = supplierInv::where('supplier_id', 'like', "SPL-%-$categoryId")
                ->orderByDesc('supplier_id')
                ->first();

            if ($lastSupplier && preg_match('/SPL-(\d{4})-' . $categoryId . '/', $lastSupplier->supplier_id, $matches)) {
                $nextSequence = intval($matches[1]) + 1;
            } else {
                $nextSequence = 1;
            }

            $sequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            $supplier_id = "SPL-{$sequence}-{$categoryId}";

            supplierInv::create([
                'supplier_id' => $supplier_id,
                'name' => $this->data['name'],
                'category_id' => $this->data['category_id'],
                'subcategory_id' => $this->data['subcategory_id'],
                'contact_person' => $this->data['contact_person'],
                'contact_number' => $this->data['contact_number'] ?? '',
                'address' => $this->data['address'] ?? '',
                'remarks' => $this->data['remarks'] ?? '',
                'added_by' => Auth::user()->username,
            ]);

            $this->emit('refreshComponent');
            $this->reset(['data']);
            $this->isOpen = false;
            $this->step = 1;
            session()->flash('messageInsert', 'Supplier successfully created!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('errorInsert', 'Validation failed: ' . json_encode($e->errors()));
        } catch (\Exception $e) {
            session()->flash('errorInsert', 'Failed to save supplier: ' . $e->getMessage());
        }
    }
}