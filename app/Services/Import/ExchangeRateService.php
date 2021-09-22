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
        $rates = json_decode($response->body());
        if(!isset($rates->data)){
            $res = '1.36';
        }else {
            foreach ($rates->data as $rate) {
                $res = $rate->USD;
            }
        }
        return $res;

    }
}
