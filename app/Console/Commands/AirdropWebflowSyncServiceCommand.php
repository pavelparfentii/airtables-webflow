<?php

namespace App\Console\Commands;

use App\Service\AirtableWebflowSyncService;
use App\Service\Mapper\CategoryMapper;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;
use Tapp\Airtable\Facades\AirtableFacade as Airtable;



class AirdropWebflowSyncServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airdrop-webflow-sync:command';



    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $universitySyncService = new AirtableWebflowSyncService(
            'Universities',
            'https://api.webflow.com/v2/collections/66b5d2eb8c401c42d7d853f0/items'
        );
        $universitySyncService->sync();

//        $coursesSyncService = new AirtableWebflowSyncService(
//            'Courses',
//            'https://api.webflow.com/v2/collections/66ebddafef241d4ff8b308d1/items',
//
//        );
//        $coursesSyncService->sync();

        $siteId = '664c7389e48704408a488d5c';
        $url = 'https://api.webflow.com/v2/sites/'. $siteId .'/publish';

        $response = Http::withToken(config('services.webflow.api_key'))->post($url, ['customDomains'=>[]]);

        if($response->successful()){
            Log::info('site published successfull');
        }else{
            Log::info('site not published ' .$response->body() );
        }

        return Command::SUCCESS;
    }
}
