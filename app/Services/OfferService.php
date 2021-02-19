<?php


namespace App\Services;


use App\Models\Offer;

class OfferService
{
    public function makeOffer(Offer $offer, BitbayService $bitbayApi){
        switch($offer->type){
            case 'sell':
            case 'SELL':
                $apiResponse = $bitbayApi->createSellOffer(
                    $offer->market,
                    $offer->amount,
                    $offer->rate
                );
            break;
            case 'buy':
            case 'BUY':
                $apiResponse = $bitbayApi->createBuyOffer(
                    $offer->market,
                    $offer->amount,
                    $offer->rate
                );
            break;
        }

        $resposne = json_decode($apiResponse, true);

        $offer->update([
            'status' => $resposne['status'],
            'completed' => $resposne['completed'] ?? false,
            'offerId' => $resposne['offerId'] ?? null,
            'errors' => $resposne['errors'] ?? null,
        ]);

        return Offer::find($offer->id);
    }
}
