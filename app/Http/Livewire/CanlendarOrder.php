<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\OrderReciept;
use App\Models\OrderRecieptItem;
use App\Models\Order;

class CanlendarOrder extends Component
{
    public $orders;

  public function mount()
    {
        $this->orders = Order::all()->map(function ($order) {
            return [
                'id' => $order->id,
                        'title' => $order->order_id . '<br>' . $order->name,
                'start' => $order->deadline,
                'color' => now()->addDay()->toDateString() === \Carbon\Carbon::parse($order->deadline)->toDateString()
                    ? '#ff4d4d'
                    : '#3788d8',
            ];
        });
    }

    public function render()
    {
        return view('livewire.canlendar-order', [
            'orders' => $this->orders,
        ]);
    }

}
