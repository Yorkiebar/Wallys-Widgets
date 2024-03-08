<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['customer_name', 'order_amount'];

    /** Relationships */
    public function packs() {
        return $this->hasManyThrough(
            WidgetPack::class,
            OrderPackLinker::class,
            'order_id',
            'id',
            'id',
            'pack_id'
        );
    }
    public function packLinkers() {
        return $this->hasMany(OrderPackLinker::class, 'order_id', 'id');
    }

    public function getPacksStrAttribute() {
        if ($this->packLinkers()->count() <= 0)
            return '-';
        $str = '';
        foreach($this->packLinkers as $packLinker) {
            $str .= $packLinker->quantity.' x '.$packLinker->getPack()->amount.'<br/>';
        }
        return $str;
    }

    public function calculatePacks() {
        $available_packs = WidgetPack::orderBy('amount', 'asc')->pluck('amount', 'id')->toArray();
        $available_packs_reversed = array_reverse($available_packs);
        // dd($available_packs, $available_packs_reversed);

        $ongoing_amount = $this->order_amount;
        $packs_to_use = [];
        
        $smallest_available_pack_id = array_keys($available_packs)[0];
        $smallest_available_pack = $available_packs[$smallest_available_pack_id];

        // Loop through to find the biggest packs which can make up this order
        while ($ongoing_amount > 0) {
            foreach($available_packs_reversed as $index=>$packAmount) {
                while ($ongoing_amount >= $packAmount) {
                    if ($ongoing_amount >= $packAmount) {
                        $packID = array_search($packAmount, $available_packs);
                        
                        $packs_to_use[] = [
                            'amount' => $packAmount,
                            'id' => $packID,
                            'ongoing_before' => $ongoing_amount,
                            'ongoing_after' => ($ongoing_amount - $packAmount)
                        ];
                        $ongoing_amount -= $packAmount;
                    }
                }
            }
            if ($ongoing_amount > 0 && $ongoing_amount <= $smallest_available_pack) {
                $packs_to_use[] = [
                    'amount' => $smallest_available_pack,
                    'id' => $smallest_available_pack_id,
                    'ongoing_before' => $ongoing_amount,
                    'ongoing_after' => ($ongoing_amount - $packAmount)
                ];
                $ongoing_amount -= $smallest_available_pack;
            }
        }

        // Double check wastage of single widgets
        if ($ongoing_amount !== 0 && count($packs_to_use) > 1) {
            // Obtain last 2 packs
            $final_pack = $packs_to_use[count($packs_to_use) - 1];
            $penultimate_pack = $packs_to_use[count($packs_to_use) - 2];

            $penultimate_ongoing = $penultimate_pack['ongoing_before'];

            $total_final_pack_widgets = $final_pack['amount'] + $penultimate_pack['amount'];
            $total_final_wastage = ($total_final_pack_widgets - $penultimate_pack['ongoing_before']);

            // Find smallest pack over the final pack widgets needed (currently spread across final 2 packs)
            $smallest_usable = null;
            foreach($available_packs as $packID=>$packAmount) {
                if ($packAmount >= $penultimate_ongoing) {
                    $smallest_usable = ['id'=>$packID, 'amount'=>$packAmount];
                    break;
                }
            }

            $alternative_wastage = ($smallest_usable['amount'] - $penultimate_ongoing);

            // If 1 alternative bigger pack gives less or equal wastage to the 2 smaller packs...
            // ... swap them out to send fewer packs and waste less widgets
            if ($alternative_wastage <= $total_final_wastage) {
                array_pop($packs_to_use);
                array_pop($packs_to_use);
                $packs_to_use[] = $smallest_usable;
            }

            // dd($penultimate_pack, $final_pack, $penultimate_ongoing, $total_final_pack_widgets, $total_final_wastage, $smallest_usable, $alternative_wastage);
        }

        $packs_with_quantity = [];
        foreach($packs_to_use as $pack_to_use) {
            if (array_key_exists($pack_to_use['id'], $packs_with_quantity)) {
                $packs_with_quantity[$pack_to_use['id']] += 1;
            }else
                $packs_with_quantity[$pack_to_use['id']] = 1;
        }
        // dd($packs_to_use, $ongoing_amount, $packs_with_quantity);

        foreach($packs_with_quantity as $packID=>$quantity) {
            OrderPackLinker::create([
                'order_id' => $this->id,
                'pack_id' => $packID,
                'quantity' => $quantity
            ]);
        }
    }
}
