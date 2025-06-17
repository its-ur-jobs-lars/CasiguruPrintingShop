<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Order;

class TotalOrders extends Component
{
    public $totalDevicesCountOrders;

    protected $listeners = ['deviceAdded' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->totalDevicesCountOrders = Order::where('isActive', 1)->count();
    }

    public function render()
    {
        return view('livewire.total-orders');
    }
}
