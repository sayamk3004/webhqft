<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ETFController;



class RefreshStockData extends Command
{
    protected $signature = 'stocks:refresh';
    protected $description = 'Refresh cached stock data';

    public function handle()
    {
        $controller = new StockController();
        $controller->refreshCache();
        $this->info('Stock data cache refreshed successfully.');
    }
}
