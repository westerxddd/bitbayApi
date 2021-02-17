<?php


namespace App\Services;


use App\Classes\BitbayApi;

class BitbayService
{
    private $bitbayApi;

    public function login($publicKey, $privateKey){
        $this->bitbayApi = new BitbayApi($publicKey, $privateKey);
        return $this;
    }

    public function getTicker($market = false){
        return $this->bitbayApi->callApi(
            '/trading/ticker'.($market ? '/'.$market : ''),
            null,
            'GET'
        );
    }

    public function getActiveOffers($market){
        return $this->bitbayApi->callApi(
            '/trading/offer/'.$market,
            null,
            'GET'
        );
    }

    public function createBuyOffer($market, $amount, $rate){
        $params = [
            "amount" => $amount,
            "rate" => $rate,
            "price" => null,
            "offerType" => 'BUY',
            "mode" => "limit",
            "postOnly" => null,
            "fillOrKill" => null
        ];

        return $this->bitbayApi->callApi(
            '/trading/offer/'.$market,
            $params,
            'POST'
        );
    }

    public function getConfig($market){
        return $this->bitbayApi->callApi(
            '/trading/config/'.$market,
            null,
            'GET'
        );
    }

    public function getHistory($params = null){

        return $this->bitbayApi->callApi(
            '/trading/history/transactions',
            $params,
            'GET'
        );
    }

    public function getWalletList($params = null){
        return $this->bitbayApi->callApi(
            '/balances/BITBAY/balance',
            $params,
            'GET'
        );
    }
}
