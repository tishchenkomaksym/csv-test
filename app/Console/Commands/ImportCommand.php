<?php

namespace App\Console\Commands;

use App\Imports\Product\ProductsImport;
use App\Services\Import\ImportCsvService;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv {test?} {--path=stock.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse and import csv file to db';
    private ImportCsvService $csvService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ImportCsvService $csvService)
    {
        parent::__construct();
        $this->csvService = $csvService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $start = now();
        $this->csvService->importFile($this->option('path'), $this->argument('test') ?? '');
//        $time = $start->diffInSeconds(now());
//        $this->info('Execution time ' . $time);
        foreach (ProductsImport::$insertFailed as $error){
            if(is_array($error)){
                foreach ( $error as $item) {
                    $this->comment($item);
                }
            }else {
                $this->comment($error);
            }

        }
    }
}
