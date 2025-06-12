<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\OrderReciept;
use App\Models\OrderRecieptItem;
use App\Models\Order;
use Illuminate\Support\Collection;

class CanlendarOrder extends Component
{
    public $orders;

  public function mount()
    {
            $orderIds = Order::selectRaw('MAX(id) as id')
            ->where('isActive', 1)
            ->groupBy('order_id')
            ->pluck('id');

        $this->orders = Order::whereIn('id', $orderIds)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'title' => $order->order_id . '<br>' . $order->name,
                    'start' => $order->deadline,
                    // 'color' => now()->addDay()->toDateString() === \Carbon\Carbon::parse($order->deadline)->toDateString()
                    //     ? '#ff4d4d'
                    //     : '#3788d8',
                    'color' => now()->toDateString() === $order->deadline ? 'red' : null,
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
