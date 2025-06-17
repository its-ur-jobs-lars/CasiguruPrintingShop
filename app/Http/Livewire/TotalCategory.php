<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;

class TotalCategory extends Component
{
    public function render()
    {
        return view('livewire.total-category');
    }

    public $totalDevicesCountCategory;

    protected $listeners = ['deviceAdded' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->totalDevicesCountCategory = Category::where('isActive', 1)->count();
    }

   
}
