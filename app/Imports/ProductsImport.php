<?php

namespace App\Imports;

use App\Models\Product;
use App\Services\Import\ExchangeRateService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImport implements ToCollection
{
    use Importable;

    public string $currency;

    public array $insertFailed = [];

    public function collection(Collection $rows):void
    {
        $rows = $rows->toArray();
        $rows = $this->checkCorrectFields($rows);

        $this->validate($rows);

        $rows = $this->importRules($rows);

        foreach ($rows as $row) {
            Product::create([
                'code' => $row['code'],
                'name' => $row['name'],
                'description' => $row['description'],
                'stock_level' => $row['stock_level'],
                'price' => $row['price'],
                'added_at' => (new Carbon())->toDateTime(),
                'discontinued_at' => $row['discontinued']
            ]);
        }
    }

    public function importRules(array $rows):array
    {
        $filteredRows = [];
        $gbpToUsdRate = (new ExchangeRateService())->getGbpToUsd();
        foreach ($rows as $key => $row) {
            $price = ($row['price'] * floatval($gbpToUsdRate));
            if ($row['discontinued'] == 'yes'){
                $row['discontinued'] = (new Carbon())->format('Y-m-d h:i:s');
                $filteredRows[] =  $row;
            }elseif ($price >= 5 && $row['stock_level'] >= 10 && $price < 1000){
                $filteredRows[] =  $row;
            }
        }

        return $filteredRows;
    }

    public function checkCorrectFields(array $rows):array
    {
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
                    'stock_level',
                    'price',
                    'discontinued'
                ], $rowNames);

        foreach ( $keys as $i => $key) {
            if(preg_match('/price\s\w+\s\w+/i', $key)){
                $exploded = explode(' ', $key);
                $this->currency = end($exploded);
                $keys[$i] = 'price';
            }
        }

        $filteredRows = [];
        foreach ($rows as $row) {
            $filteredRows[] = array_combine($keys, $row);
        }

        return $filteredRows;
    }

    public function validate(array &$rows)
    {
        $validator = Validator::make($rows, [
            '*.code' => ['required', 'unique:products', 'max:10'],
            '*.name' => ['required', 'max:50'],
            '*.description' => ['required', 'max:255'],
            '*.stock_level' => ['nullable','numeric'],
            '*.price' => ['numeric'],
        ]);
        if($validator->fails()){
            foreach ($validator->errors()->messages() as $key => $error){
                $keyRemove = explode('.', $key)[0];
                $this->insertFailed[] = $error;
                unset($rows[$keyRemove]);
            }
        }


    }

}
