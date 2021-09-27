<?php

namespace App\Imports\Product;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ProductsImport implements ToCollection, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    use Importable;

    /**
     * @var string
     */
    public static string $currency;

    /**
     * @var string
     */
    public string $test;


    /**
     * @var ProductValidation
     */
    public ProductValidation $productValidation;

    public function __construct()
    {
        $this->productValidation = new ProductValidation();
    }

    public function collection(Collection $rows): void
    {
        $rows = $rows->toArray();
        $rows = $this->productValidation->checkCorrectFields($rows);

        $this->productValidation->validate($rows);

        $rows = $this->productValidation->importRules($rows);
        if ($this->test === 'test') {
            return;
        }
        foreach ($rows as $row) {
            Product::create([
                'code' => $row['code'],
                'name' => $row['name'],
                'description' => $row['description'],
                'stock' => $row['stock'],
                'price' => $row['price'],
                'added_at' => (new Carbon())->toDateTime(),
                'discontinued_at' => $row['discontinued']
            ]);
        }


    }


    /**
     * batch setting for optimization
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * chunk setting for optimization
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

}
