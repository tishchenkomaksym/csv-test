<?php

namespace App\Imports\Product;

use App\Services\Import\ExchangeRateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class ProductValidation
{
    private array $usdRate = [];
    private array $keys = [];
    public $gbpToUsdRate;
    public array $insertedFail = [];

    /**
     * @param array $rows
     *
     * @return array
     */
    public function importRules(array $rows):array
    {
        $filteredRows = [];
        $exchangeRateService = new ExchangeRateService();
        /**
         * Fix too many request for getting exchange rate
         */
        if ($this->gbpToUsdRate){
            $gbpToUsdRate = $this->gbpToUsdRate;
        }else {
            $gbpToUsdRate = $exchangeRateService->getGbpToUsd();
             $this->gbpToUsdRate = $gbpToUsdRate;
        }


        foreach ($rows as $key => $row) {

            foreach ($row as $i => $item) {
                str_replace("\r", "\n", str_replace("\r\n", "\n", $row[$i]));
            }

            $price = $this->checkUsdField($key, $row, $gbpToUsdRate);
            $row['price'] = $price;
            if ($row['discontinued'] == 'yes'){
                $row['discontinued'] = (new Carbon())->format('Y-m-d h:i:s');
                $filteredRows[] =  $row;
            }elseif ($price >= 5 && $row['stock'] >= 10 && $price < 1000){
                $filteredRows[] =  $row;
            }else {
                $this->insertedFail[] = 'The ' . $key . ' did not match import rules';
            }
        }

        return $filteredRows;
    }

    /**
     * check if usd field that do not exchange to gbp
     * @param $key
     * @param $row
     * @param $gbpToUsdRate
     *
     * @return string
     */
    private function checkUsdField($key, $row, $gbpToUsdRate):string
    {
        if (!in_array($key, $this->usdRate)){
            $price = ($row['price'] * floatval($gbpToUsdRate));
        }else {
            $price = $row['price'];
        }
        return $price;
    }

    /**
     * check correct fields
     * @param array $rows
     *
     * @return array
     */
    public function checkCorrectFields(array $rows):array
    {
        if (empty($this->keys)){
            $rowNames = array_shift($rows);

            $keys =  preg_replace(
                [
                    '/product.code/i',
                    '/product.name/i',
                    '/product.description/i',
                    '/stock/i',
                    '/cost|price/i',
                    '/discontinued/i',
                ],
                [
                    'code',
                    'name',
                    'description',
                    'stock',
                    'price',
                    'discontinued'
                ], $rowNames);
            /**
             * save keys for using chunks
             */
            $this->keys = $keys;
        }else {
            $keys = $this->keys;
        }


        foreach ( $keys as $i => $key) {
            if(preg_match('/price\s\w+\s\w+/i', $key)){
                $exploded = explode(' ', $key);
                /**
                 * TODO May be handle currency ticker
                 */
                ProductsImport::$currency = end($exploded);
                $keys[$i] = 'price';
            }
        }

        $filteredRows = [];
        foreach ($rows as $row) {
            $filteredRows[] = array_combine($keys, $row);
        }

        return $filteredRows;
    }

    /**
     * @param array $rows
     */
    public function validate(array &$rows):void
    {
        /**
         * check if price in dollars $
         */
        foreach ( $rows as $key => $row ) {
            if (is_string($row['price']) && $row['price'][0] === '$') {
                $rows[$key]['price'] = substr($row['price'], 1);
                $this->usdRate[] = $key;
            }
        }

        $validator = Validator::make($rows, [
            '*.code' => ['required', 'unique:products', 'max:10'],
            '*.name' => ['required', 'max:50'],
            '*.description' => ['required', 'max:255'],
            '*.stock' => ['nullable','numeric'],
            '*.price' => ['numeric'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $error){
                $keyRemove = explode('.', $key)[0];
                /**
                 * collect not inserted row for showing in console
                 */
                $this->insertedFail[] = $error;
                unset($rows[$keyRemove]);
            }
        }

    }
}
