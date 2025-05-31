<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\NumberInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use Livewire\WithFileUploads;


class CategoryAdd extends Component
{

     use WithFileUploads; 
     public $data = [];

     

    //  public $showPreview = false;

    public bool $isOpen = false; // Controls modal visibility

//     public function showImagePreview()
// {
//     $this->showPreview = true;
// }

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
                'data.category_name' => 'required',
                'data.description' => 'nullable',
                'image' => 'nullable|image|max:2048',
            ]);

            if ($this->image) {
                $imagePath = $this->image->store('images', 'public');
                $this->data['image'] = $imagePath;
            }


            // Get the last category globally
        $lastCategory = Category::where('category_id', 'like', 'CTG-%')
            ->orderByDesc('category_id')
            ->first();

        if ($lastCategory && preg_match('/CTG-(\d{4})$/', $lastCategory->category_id, $matches)) {
            $nextSequence = intval($matches[1]) + 1;
        } else {
            $nextSequence = 1;
        }
        $sequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        $category_id = "CTG-{$sequence}";


            $categoryName = $this->data['category_name']; 


            Category::create([
                'category_id'   => $category_id,
                'category_name' => $this->data['category_name'],
                'description'   => $this->data['description'],
                'added_by'      => Auth::user()->username,
            ]);

      
            
            // Emit an event to refresh the table
        $this->emit('refreshTable');

       
        $this->reset(['data', 'image']);
        $this->isOpen = false;
      

            // Show a success message
            session()->flash('messageInsert', 'Category "' . $categoryName . '" added successfully!');
        } catch (\Exception $e) {
            // Handle the exception and show an error message
            session()->flash('errorInsert', $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.category-add');
    }
}


