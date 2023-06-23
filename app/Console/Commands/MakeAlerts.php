<?php

namespace App\Console\Commands;

use App\Http\Controllers\MakeTest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MakeAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:makeAlerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute the pingScenarios function every hour';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $pingScenarios = new MakeTest();
            $r = $pingScenarios->pingScenarios();
            $currentTime = Carbon::now()->toDateTimeString();
            $logMessage = "Execution succeeded on $currentTime";
            Log::info($logMessage);
            Log::info($r);
        } catch (\Exception $e) {
            $errorMessage = 'An error occurred while executing pingScenarios: ' . $e->getMessage();
            Log::error($errorMessage);
        }
        
    }
}
