<?php

namespace Tests\Unit;

use App\Services\Import\ExchangeRateService;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetGbpToUsd()
    {
        $exchangeRateService = new ExchangeRateService();
        $res = $exchangeRateService->getGbpToUsd();
        $this->assertIsNumeric($res);
    }
}
