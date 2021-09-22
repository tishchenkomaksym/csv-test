<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    /**
     * @return string
     * during testing Error too many request
     */
    public function getGbpToUsd():string
    {
        $res = '';
        $url = "https://freecurrencyapi.net/api/v1/rates?base_currency=GBP";
        $response = Http::get($url);
        if(!isset($response->body()->data)){
            $res = '1.36';
        }else {
            $rates = json_decode($response->body())->data;
            foreach ( $rates as $rate) {
                $res = $rate->USD;
            }
        }
        return $res;

    }
}
