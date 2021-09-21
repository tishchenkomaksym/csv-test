<?php

namespace App\Imports;

use App\Models\Product;
use App\Services\Import\ExchangeRateService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImport implements ToCollection
{
    use Importable;

    public array $insertFailed = [];

    public function collection( Collection $rows ):void
    {
        $rows = $rows->toArray();
        array_shift($rows);

        $this->validate($rows);

        $rows = $this->importRules($rows);

        foreach ($rows as $key => $row) {
//            var_dump($row);
            Product::create([
                'code' => $row[0],
                'name' => $row[1],
                'description' => $row[2],
                'stock_level' => $row[3],
                'price' => $row[4],
                'added_at' => (new Carbon())->toDateTime(),
                'discontinued_at' => $row[5]
            ]);
        }
    }

    public function importRules(array $rows):array
    {
        $filteredRows = [];
        $gbpToUsdRate = (new ExchangeRateService())->getGbpToUsd();
        foreach ( $rows as $key => $row) {
            if ($row[5] == 'yes'){
                $row[5] = (new Carbon())->format('Y-m-d h:i:s');
                $filteredRows[] =  $row;
            }elseif (($row[4] * floatval($gbpToUsdRate)) >= 5 && $row[3] >= 10){
                $filteredRows[] =  $row;
            }
        }

        return $filteredRows;
    }

    public function validate(array &$rows)
    {
        $filteredRows = [];
        foreach ( $rows as $row) {
            $keys = $this->validatedKeys();
            $filteredRows[] = array_combine($keys, $row);
        }
//        var_dump($filteredRows);
//        die();
        $validator = Validator::make($filteredRows, [
            '*.code' => ['required', 'unique:products', 'max:10'],
            '*.name' => ['required', 'max:50'],
            '*.description' => ['required', 'max:255'],
            '*.stock_level' => ['nullable','numeric'],
            '*.price' => ['numeric'],
        ]);
        if($validator->fails()){
            foreach ($validator->errors()->messages() as $key => $error){
                $keyRemove = explode('.', $key)[0];
                unset($rows[$keyRemove]);
            }
//            var_dump($validator->errors());
        }


    }

    public function validatedKeys():array
    {
        return [
            'code',
            'name',
            'description',
            'stock_level',
            'price',
            'discontinued'
        ];
    }
}
