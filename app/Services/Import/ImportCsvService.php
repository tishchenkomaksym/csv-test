<?php

namespace App\Services\Import;

use App\Imports\Product\ProductsImport;
use Maatwebsite\Excel\Excel;

class ImportCsvService
{
    /**
     * @param $path
     * @param null $test
     *
     * @return string|void
     */
    public function importFile($path, $test = null)
    {
        $productImport = new ProductsImport();
        $productImport->test = $test;
        try {
            $productImport->import(base_path($path), null, Excel::CSV);
        }catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $exception) {
            return 'Wrong extension, you need csv extension';
        }catch (\Error $error) {
            return 'Some error';
        }

    }
}
