<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WidgetPack;

class WidgetPacks extends Component
{
    public bool $editing=false, $editing_available=true, $editing_used_in_orders=false;
    public $editing_id, $editing_amount;

    public function openAddNewPack() {
        $this->resetNewPackModal();
        $this->editing = true;
    }

    public function store() {
        $this->validate(['editing_amount'=>'required|numeric|min:1'], [], ['editing_amount'=>'amount']);
        
        if ($this->editing_id) {
            $pack = WidgetPack::withTrashed()->where('id', $this->editing_id)->first();
        }else
            $pack = WidgetPack::create(['amount'=>$this->editing_amount]);
        
        if (!$this->editing_available) {
            $pack->delete();
        }else{
            $pack->deleted_at = null;
            $pack->save();
        }

        $this->resetNewPackModal();
    }

    public function editPack($id) {
        $pack = WidgetPack::withTrashed()->where('id', $id)->first();
        $this->editing_id = $pack->id;
        $this->editing_amount = $pack->amount;
        $this->editing_available = ($pack->deleted_at == null);
        $this->editing_used_in_orders = ($pack->orderLinkers->count() > 0);
        $this->editing = true;
    }

    public function resetNewPackModal() {
        $this->editing = false;
        $this->editing_id = null;
        $this->editing_amount = null;
        $this->editing_used_in_orders = false;
        $this->editing_available = true;
    }

    public function render()
    {
        $widgetPacks = WidgetPack::withTrashed()->paginate(20);
        return view('livewire.widget-packs', compact('widgetPacks'));
    }
}
