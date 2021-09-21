<?php

namespace App\Services\Import;

use App\Imports\ProductsImport;
use Maatwebsite\Excel\Excel;

class ImportCsvService
{
    public function importFile()
    {
        (new ProductsImport())->import(base_path('stock.csv'), null, Excel::CSV);
    }
}
