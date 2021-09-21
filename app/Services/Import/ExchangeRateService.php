<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function getGbpToUsd():string
    {
        $res = '';
        $url = "https://freecurrencyapi.net/api/v1/rates?base_currency=GBP";
        $response = Http::get($url);
        $rates = json_decode($response->body())->data;
        foreach ( $rates as $rate) {
            $res = $rate->USD;
        }

        return $res;
    }
}
