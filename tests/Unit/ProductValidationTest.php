<?php

namespace Tests\Unit;

use App\Imports\Product\ProductValidation;
use App\Services\Import\ExchangeRateService;
use Tests\TestCase;


class ProductValidationTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testImportRuleCorrect()
    {
        $exchangeRateService = new ExchangeRateService();
        $res = $exchangeRateService->getGbpToUsd();
        $row = [
            [
                "code" => "P0001",
                "name" => "TV",
                "description" => "32 Tv",
                "stock" => 10,
                "price" => 399.99,
                "discontinued" => null
            ]
        ];
        $productValidation = new ProductValidation();
        $response = $productValidation->importRules($row);
        $row[0]['price'] *= $res;
        $this->assertEquals($row, $response);
    }
}
