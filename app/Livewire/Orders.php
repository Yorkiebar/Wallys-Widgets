<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\WidgetPack;

class Orders extends Component
{
    public bool $editing=false;
    public $editing_id, $editing_amount, $editing_customer;

    public function openAddNewOrder() {
        $this->resetOrderModal();
        $this->editing = true;
    }

    public function store() {
        $this->validate([
            'editing_amount' => 'required|numeric|min:1',
            'editing_customer' => 'required|string'
        ], [], ['editing_amount'=>'amount', 'editing_customer'=>'customer']);
        
        if ($this->editing_id) {
            $order = Order::find($this->editing_id);
        }else
            $order = Order::make();
        
        $order->customer_name = $this->editing_customer;
        $order->order_amount = $this->editing_amount;
        $order->save();
        // $order->calculatePacks();

        $this->resetOrderModal();
    }

    public function resetOrderModal() {
        $this->editing = false;
        $this->editing_id = null;
        $this->editing_amount = null;
        $this->editing_customer = null;
    }

    public function render()
    {
        $orders = Order::latest()->paginate(20);
        return view('livewire.orders', compact('orders'));
    }
}
