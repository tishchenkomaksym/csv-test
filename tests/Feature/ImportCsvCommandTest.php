<?php

namespace Tests\Feature;

use Tests\TestCase;

class ImportCsvCommandTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testImportCsvCommandValidationDescription()
    {
        $this->artisan('import:csv test --path="stock-test2.csv"')
             ->expectsOutput('The 4.price must be a number.');
    }

    public function testImportDatabase()
    {
        $this->artisan('import:csv --path="stock-test.csv"')
             ->expectsOutput('The 3 did not match import rules');
        $this->assertDatabaseHas('products', [
            'name' => 'TV',
            'code' => 'P0001'
        ]);
    }

    public function testImportCsvCommandWithWrongExtension()
    {
        $this->artisan('import:csv test --path="test-list.xlsx"')
             ->doesntExpectOutput('Wrong extension, you need csv extension');
    }

}
