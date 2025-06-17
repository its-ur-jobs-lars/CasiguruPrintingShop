<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Pricelist;

class TotalPriceList extends Component
{
    public function render()
    {
        return view('livewire.total-price-list');
    }


    public $totalDevicesCountPricelist;

    protected $listeners = ['deviceAdded' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->totalDevicesCountPricelist = Pricelist::where('isActive', 1)->count();
    }

}


