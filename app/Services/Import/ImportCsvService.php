<?php

namespace App\Services\Import;

use App\Imports\ProductsImport;
use Maatwebsite\Excel\Excel;

class ImportCsvService
{
    public array $insertFailed = [];

    public function importFile()
    {
        $productImport = new ProductsImport();
        $productImport->import(base_path('stock.csv'), null, Excel::CSV);
        $this->insertFailed = $productImport->insertFailed;
    }
}
