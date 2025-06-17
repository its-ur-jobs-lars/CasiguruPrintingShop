<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\SubCategory;

class TotalServices extends Component
{
    public function render()
    {
        return view('livewire.total-services');
    }

    public $totalDevicesCountServices;

    protected $listeners = ['deviceAdded' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->totalDevicesCountServices = SubCategory::where('isActive', 1)->count();
    }

}

