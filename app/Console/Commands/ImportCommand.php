<?php

namespace App\Console\Commands;

use App\Services\Import\ImportCsvService;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv';

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
        $this->csvService->importFile();
    }
}
