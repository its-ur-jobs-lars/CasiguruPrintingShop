<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\NumberInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Pricelist;
use Illuminate\Support\Facades\Auth;


class PriceListItem extends Component
{
    public function render()
    {
        return view('livewire.price-list-item');
    }

     public $Category = [];
    public $SubCategory = [];
      public function mount()
    {
        $this->Category = Category::where(function ($query) {
            $query->where('isActive', 1);
        })->get();

        $this->SubCategory = SubCategory::where(function ($query) {
            $query->where('isActive', 1);
        })->get();

    }

     public $data = [];


    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public bool $isOpen = false; // Controls modal visibility

    public function save()
{
    try {

   $this->validate([
    'data.category_id' => 'required',
    'data.subcategory_id' => 'required',
    'data.price_10_50' => 'required',
    'data.price_51_100' => 'required', 
    'data.price_101_500' => 'required', 
    'data.remarks' => 'required',
]);

        // Fetch the category name using the selected category_id
        $category = Category::find($this->data['category_id']);
        $categoryName = $category ? $category->category_name : '';

        // Get the first 3 letters of the category name, uppercase
        $prefix = strtoupper(substr(str_replace(' ', '', $categoryName), 0, 3));

        // Find the last price list item with this prefix
        $lastItem = Pricelist::where('pricelist_id', 'like', "PLT-{$prefix}-%")
            ->orderByDesc('pricelist_id')
            ->first();

        if ($lastItem && preg_match('/-(\d{4})$/', $lastItem->pricelist_id, $matches)) {
            $nextSequence = intval($matches[1]) + 1;
        } else {
            $nextSequence = 1;
        }
        $sequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        $pricelist_id = "PLT-{$prefix}-{$sequence}";

       

        // Create a new price list item
        Pricelist::create([
            'pricelist_id' => $pricelist_id,
            'category_id' => $this->data['category_id'],
            'subcategory_id' => $this->data['subcategory_id'],
            'price_10_50' => $this->data['price_10_50'],
            'price_51_100' => $this->data['price_51_100'],
            'price_101_500' => $this->data['price_101_500'],
            'remarks' => $this->data['remarks'],
            'added_by' => Auth::user()->username,
        ]);



        // Emit an event to refresh the table
        $this->emit('refreshComponent');

        // Reset the form data and close the modal
        $this->reset('data');
        $this->isOpen = false;

        // Show a success message
        session()->flash('messageInsert', 'Pricelist is added successfully!');
    } catch (\Exception $e) {
        // Handle the exception and show an error message
        session()->flash('errorInsert', $e->getMessage());
    }
}

}

