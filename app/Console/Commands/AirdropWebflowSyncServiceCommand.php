<?php

namespace App\Console\Commands;

use App\Service\AirtableWebflowSyncService;
use App\Service\Mapper\CategoryMapper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
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

        $coursesSyncService = new AirtableWebflowSyncService(
            'Courses',
            'https://api.webflow.com/v2/collections/66ebddafef241d4ff8b308d1/items',

        );
        $coursesSyncService->sync();

        return Command::SUCCESS;
    }
}
