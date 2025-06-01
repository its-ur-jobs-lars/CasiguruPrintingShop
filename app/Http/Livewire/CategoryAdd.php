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


class CategoryAdd extends Component
{

    
    public $data = [];
    public bool $isOpen = false;

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function save()
    {
        try {
            $this->validate([
                'data.category_name' => 'required',
                'data.description' => 'nullable',
            ]);

            // Generate prefix using first letter of each word in uppercase
            $words = explode(' ', strtoupper($this->data['category_name']));
            $prefix = '';
            foreach ($words as $word) {
                $prefix .= substr($word, 0, 1);
            }

            // Find last matching category ID with the same prefix
            $lastCategory = Category::where('category_id', 'like', "CTG-{$prefix}-%")
                ->orderByDesc('category_id')
                ->first();

            $nextSequence = 1;
            if ($lastCategory && preg_match('/-(\d{4})$/', $lastCategory->category_id, $matches)) {
                $nextSequence = intval($matches[1]) + 1;
            }

            $sequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            $category_id = "CTG-{$prefix}-{$sequence}";

            // Save new category
            Category::create([
                'category_id'   => $category_id,
                'category_name' => $this->data['category_name'],
                'description'   => $this->data['description'],
                'added_by'      => Auth::user()->username,
            ]);

            // Reset and close modal
            $this->emit('refreshComponent');
            $this->reset(['data']);
            $this->isOpen = false;

            session()->flash('messageInsert', 'New category is added successfully!');
        } catch (\Exception $e) {
            session()->flash('errorInsert', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.category-add');
    }
}









