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
    public $Category = [];
    public $SubCategory = [];
    public bool $isOpen = false; // Controls modal visibility

    public function render()
    {
        return view('livewire.price-list-item');
    }


     public $data = [];

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
        if (! $this->SubCategory->contains('subcategory_id', $this->data['subcategory_id'] ?? null)) {
            $this->data['subcategory_id'] = null;
        }
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function rules()
{
    return [
        'data.category_id' => 'required|exists:category,category_id',
        'data.subcategory_id' => 'required|exists:sub-category,subcategory_id',
        'data.remarks' => 'nullable|string|max:255',

        // Universal price fields
        'data.price_1' => 'nullable|numeric|min:0',
        'data.price_2_50' => 'nullable|numeric|min:0',
        'data.price_51_100' => 'nullable|numeric|min:0',
        'data.price_101_500' => 'nullable|numeric|min:0',
        'data.price_501_999' => 'nullable|numeric|min:0',
        'data.price_1000_up' => 'nullable|numeric|min:0',
    ];
}


    // public function rules()
    // {
    //     $categoryId = $this->data['category_id'] ?? null;
    //     $category = $categoryId ? Category::find($categoryId) : null;
    //     // Normalize category name for lookup
    //     $categoryName = strtolower(trim($category->category_name ?? ''));

    //     $priceFieldsByCategory = [
    //         't-shirt with print' => [
    //             'price_1',
    //             'price_2_50',
    //             'price_51_999',
    //             'price_1000_up',
    //         ],
    //         'full sublimation printing' => [
    //             'price_10_50',
    //             'price_51_100',
    //             'price_101_500',
    //         ],
    //     ];

    //     $rules = [
    //         'data.category_id' => ['required', 'exists:category,category_id'],
    //         'data.subcategory_id' => ['required', 'exists:sub-category,subcategory_id'],
    //         'data.remarks' => ['required', 'string'],
    //     ];

    //     // Only validate fields specific to the selected category
    //     $fields = $priceFieldsByCategory[$categoryName] ?? [];
    //     foreach ($fields as $field) {
    //         $rules["data.$field"] = ['required', 'numeric'];
    //     }

    //     return $rules;
    // }

    public function nextStep() { $this->step = 2; }
     public function previousStep() { $this->step = 1; }

    public function save()
    {
        try {
           $this->validate($this->rules());

        $category = Category::find($this->data['category_id']);
        $subcategory = Subcategory::find($this->data['subcategory_id']);

        // Get initials from category and subcategory names
        $categoryInitials = collect(explode(' ', $category->category_name ?? ''))
            ->filter()
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');

        $subcategoryInitials = collect(explode(' ', $subcategory->subcategory_name ?? ''))
            ->filter()
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');

        $prefix = "{$categoryInitials}-{$subcategoryInitials}";

        // Find last matching pricelist ID
        $lastItem = Pricelist::where('pricelist_id', 'like', "PLT-{$prefix}-%")
            ->orderByDesc('pricelist_id')
            ->first();

        // Determine next sequence number
        $nextSequence = 1;
        if ($lastItem && preg_match('/-(\d{4})$/', $lastItem->pricelist_id, $matches)) {
            $nextSequence = intval($matches[1]) + 1;
        }
        $sequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

        // Final ID
        $pricelist_id = "PLT-{$prefix}-{$sequence}";

        // Universal price fields
        $universalPriceFields = [
            'price_1',
            'price_2_50',
            'price_51_100',
            'price_101_500',
            'price_501_999',
            'price_1000_up',
        ];

        // Collect price data
        $priceData = [];
        foreach ($universalPriceFields as $field) {
            $priceData[$field] = $this->data[$field] ?? null;
        }

        // Save
        Pricelist::create(array_merge([
            'pricelist_id' => $pricelist_id,
            'category_id' => $this->data['category_id'],
            'subcategory_id' => $this->data['subcategory_id'],
            'remarks' => $this->data['remarks'],
            'added_by' => Auth::user()->username,
        ], $priceData));

            $this->emit('refreshComponent');
            $this->reset('data');
            $this->isOpen = false;
            session()->flash('messageInsert', 'Pricelist is added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('errorInsert', json_encode($e->errors()));
        } catch (\Exception $e) {
            session()->flash('errorInsert', $e->getMessage());
        }
    }
}

