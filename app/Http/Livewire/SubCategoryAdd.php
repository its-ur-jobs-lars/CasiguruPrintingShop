<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\NumberInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use App\Models\productModel;
use App\Models\productTypemodel;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\SubCategory;
use Livewire\WithFileUploads;

class SubCategoryAdd extends Component
{
     use WithFileUploads; 

     public $Category = [];
      public function mount()
    {
        $this->Category = Category::where(function ($query) {
            $query->where('isActive', 1);
        })->get();

    }

    //  public $showPreview = false;

    public bool $isOpen = false; // Controls modal visibility

           public $selectedCategoryName;
            public $data = [
                'category_id' => '',
                'subcategory_name' => '',
                'size' => '',
            ];
            public function updated($propertyName)
            {
                if ($propertyName === 'data.category_id') {
                    $category = Category::find($this->data['category_id']);
                    $this->selectedCategoryName = $category?->category_name;
                }
            }



    public function openModal()
    {
        $this->isOpen = true;
        $this->step = 1;
    }

    public function closeModal1()
    {
        $this->isOpen = false;
    }

       public $image;
    public $imageUploaded = false;

    public function updatedImage()
    {
        $this->imageUploaded = true;
    } 
    
    public function save()
{
    try {
        $this->validate([
            'data.category_id' => 'required',
            'data.subcategory_name' => 'required',
            'data.description' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($this->image) {
            $imagePath = $this->image->store('images', 'public');
            $this->data['image'] = $imagePath;
        }

        // Generate subcategory_id using first letters of subcategory_name and category
        $catCon = $this->getFirstLetters($this->data['subcategory_name']);
        $category = Category::where('category_id', $this->data['category_id'])->first();
        $subCon = $category ? $this->getFirstLetters($category->category_name) : 'XXX';

        // Get the last subcategory globally
        $lastSubCategory = SubCategory::where('subcategory_id', 'like', "CTG-%-%-%")
            ->orderByDesc('subcategory_id')
            ->first();

        if ($lastSubCategory && preg_match('/-(\d{4})$/', $lastSubCategory->subcategory_id, $matches)) {
            $nextSequence = intval($matches[1]) + 1;
        } else {
            $nextSequence = 1;
        }
        $sequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        $subcategory_id = "CTG-{$catCon}-{$subCon}-{$sequence}";

        $SubcategoryName = $this->data['subcategory_name']; 

            SubCategory::create([
            'subcategory_id'   => $subcategory_id,
            'category_id'      => $this->data['category_id'],
            'subcategory_name' => $this->data['subcategory_name'],
            'description'      => $this->data['description'],
            'image'            => $this->data['image'] ?? null,
            'added_by'         => Auth::user()->username,
        ]);

        
        $this->emit('refreshComponent', now());

        $this->reset(['data', 'image']);
        $this->isOpen = false;

        session()->flash('messageInsert', 'SubCategory "' . $SubcategoryName . '" added successfully!');
    } catch (\Exception $e) {
        session()->flash('errorInsert', $e->getMessage());
    }
}

    // Helper: Get first letter of each word, uppercase
    private function getFirstLetters($string)
    {
        if (!$string) return '';
        preg_match_all('/\b(\w)/', $string, $matches);
        return strtoupper(implode('', $matches[1]));
    }

public function render()
{
    return view('livewire.sub-category-add', [
        'Category' => Category::all(),
    ]);
}

}


