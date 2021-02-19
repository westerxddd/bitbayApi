<?php

namespace App\Observers;

use App\Models\Offer;

class OfferObserver
{
    public function creating(Offer $offer){
        $offer->price = $offer->amount * $offer->price;
    }
}
