<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;
use Tapp\Airtable\Facades\AirtableFacade as Airtable;

class AirdropWebflowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airdrop:webflow';

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
        $airtableUrl = "https://api.airtable.com/v0/appKNOyMC0fPOLylY/Universities";
        $webflowResponseListItems = $this->getWebflowItems();
//        dd($webflowResponseListItems);
        $webflowUrl = "https://api.webflow.com/v2/collections/66b5d2eb8c401c42d7d853f0/items";

        $response = Http::withToken(config('services.airtable.api_key'))->get($airtableUrl);

        //dd($response->json()['records']);

        $a = Airtable::table('Courses')->all();
//        dd($a);

//        $records = $response->json()['records'];

        $records = Airtable::table('Universities')->all();

        $webflowSlugs = [];
        foreach ($webflowResponseListItems as $key => $item) {
            $webflowSlugs[$item] = $key;
        }

        $itemsToUpdate = [];

// Проходимо по кожному запису з $records
        if (isset($records)) {
            foreach ($records as $record) {
                $fields = $record['fields'];

                if (isset($fields) && !empty($fields)) {
                    // Отримуємо значення last_modified і обробляємо його, якщо присутній
                    if (isset($fields['last_modified']) && !empty($fields['last_modified'])) {
                        $lastModified = $fields['last_modified'];
                        $lastModifiedDate = Carbon::parse($lastModified);
                    }

                    // Створюємо slug з Name
                    $slug = $this->slugify($fields['Name']);

                    // Перетворення Markdown та збирання даних для оновлення
                    $description = $fields['Description'] ?? null;
                    $logotypeUrl = $fields['Logotype'][0]['url'] ?? null;
                    $living = $fields['Living and Accommodation'] ?? null;
                    $heroBanner = $fields['Hero Banner'][0]['url'] ?? null;
                    $educationPrograms = $fields['Education/Programms'] ?? null;
                    $globalRanking = $fields['Global Ranking URL'] ?? null;
                    $locationCity = $fields["City"] ?? null;
                    $localRating = $fields["Local Ranking"] ?? null;
                    $aboutUniversity = $fields["About University"] ?? null;
                    $courses = $fields["Courses/Programms"] ?? null;

                    $converter = new CommonMarkConverter();
                    $formatLiving = isset($living) ? $converter->convert($living)->getContent() : null;
                    $formatEducationPrograms = isset($educationPrograms) ? $converter->convert($educationPrograms)->getContent() : null;
                    $formatOverview = isset($aboutUniversity) ? $converter->convert($aboutUniversity)->getContent() : null;

                    // Перевіряємо, чи існує цей slug в $webflowSlugs
                    if (isset($webflowSlugs[$slug])) {

                        // Збіг знайдено, виконуємо оновлення
//                        dd('here');

                        $fieldData = array_filter([
                            "name" => $fields['Name'],
                            "slug" => $slug,
                            "description-2" => $description,
                            "logo" => $logotypeUrl,
                            "living" => $formatLiving,
                            "hero-banner" => $heroBanner,
                            "university-programs" => $formatEducationPrograms,
                            "global-ranking" => $globalRanking,
                            "location-city" => $locationCity,
                            "locar-ranking" => $localRating,
                            "overview" => $formatOverview,
                            "available-courses" => $courses
                        ], function ($value) {
                            return !is_null($value);
                        });


                        // Додаємо запис для оновлення лише якщо дата збігається
                        if ($lastModifiedDate->isSameDay(now())) {

                            $itemsToUpdate[] = [
                                "id" => $webflowSlugs[$slug],
                                "isArchived" => false,
                                "isDraft" => false,
                                "fieldData" => $fieldData
                            ];

                        }
                    } else {
                        // Якщо slug не знайдено в $webflowSlugs, формуємо новий запис для $fieldDataPostToWebflow
                        $fieldDataPostToWebflow[$slug] = array_filter([
                            "name" => $fields['Name'],
                            "slug" => $slug,
                            "description-2" => $fields['Description'] ?? null,
                            "logo" => $fields['Logotype'][0]['url'] ?? null,
                            "living" => isset($fields['Living and Accommodation']) ? $converter->convert($fields['Living and Accommodation'])->getContent() : null,
                            "hero-banner" => $fields['Hero Banner'][0]['url'] ?? null,
                            "university-programs" => isset($fields['Education/Programms']) ? $converter->convert($fields['Education/Programms'])->getContent() : null,
                            "global-ranking" => $fields['Global Ranking URL'] ?? null,
                            "location-city" => $fields['City'] ?? null,
                            "locar-ranking" => $fields['Local Ranking'] ?? null,
                            "overview" => isset($fields['About University']) ? $converter->convert($fields['About University'])->getContent() : null,
                            "available-courses" => $fields['Courses/Programms'] ?? null
                        ], function ($value) {
                            return !is_null($value);
                        });
                    }
                }
            }
        }


        $fieldDataPostToWebflow = isset($fieldDataPostToWebflow) ? array_values($fieldDataPostToWebflow) : null;
//        dd(empty($fieldDataPostToWebflow));

        $data = [
            "items" => $itemsToUpdate
        ];


        $newData = [
            "cmsLocaleIds" => [], // додайте необхідні локалізації
            "isArchived" => false,
            "isDraft" => false,
            "fieldData" => $fieldDataPostToWebflow
        ];



        if(!empty($data['items'])){
            $webflowResponse = Http::withToken(config('services.webflow.api_key'))
                ->patch($webflowUrl, $data);

            if ($webflowResponse->successful()) {
                var_dump($webflowResponse->json());
                $this->publishItems($webflowResponse->json());

//                return response()->json(['message' => 'Items updated in Webflow successfully']);
            } else {
                var_dump($webflowResponse->json());
                Log::info('Failed to update items in Webflow'. $webflowResponse->body());

            }
        }

        if(!empty($newData['fieldData'])){
            $postNewCollectionItemsToWebflowUrl = "https://api.webflow.com/v2/collections/66b5d2eb8c401c42d7d853f0/items/bulk";
            $webflowResponse = Http::withToken(config('services.webflow.api_key'))
                ->post($postNewCollectionItemsToWebflowUrl, $newData);

            if ($webflowResponse->successful()) {
                var_dump('new collection');
                var_dump($webflowResponse->json());
                $this->publishItems($webflowResponse->json());
                Log::info('Items updated in Webflow successfully');
//                return response()->json(['message' => 'Items updated in Webflow successfully']);
            } else {
                var_dump($webflowResponse->json());
                Log::info('Items updated in Webflow successfully' . $webflowResponse->body());
//                return response()->json(['error' => 'Failed to update items in Webflow', 'details' => $webflowResponse->body()], 500);
            }
        }

        return Command::SUCCESS;

//        dd('here');
////        return 0;
////        $airtable = new Airtable(['api_key' => 'your_api_key',
////            'base' => 'your_base_id']);
////        $records= $airtable::table('Universities')->all();
//       $records = Airtable::table('Universities')->all();
//       foreach ($records as $record) {
//           if (isset($record['fields']) && !empty($record['fields'])) {
//               if (isset($record['fields']['last_modified']) && !empty($record['fields']['last_modified'])) {
//                   $lastModified = $record['fields']['last_modified'];
//                   $lastModifiedDate = Carbon::parse($lastModified);
//
//                   if ($lastModifiedDate->isSameDay(now())){
//                       dd('here');
//                   }
//               }
//
//           }
//       }
//        dd($records);
    }

    private function slugify($text, string $divider = '-')
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    private function getWebflowItems()
    {

        $collection = "66b5d2eb8c401c42d7d853f0";

        $url = "https://api.webflow.com/v2/collections/$collection/items";
        $offset = 0;
        $limit = 100;
        $allItems = [];

        do {

            $response = Http::withToken(config('services.webflow.api_key'))->get($url, [
                'offset' => $offset,
                'limit' => $limit,
            ]);


            if ($response->successful()) {
                $data = $response->json();


                if(isset($data['items']) && !empty($data['items'])){
                    foreach ($data['items'] as $item) {
                        $allItems[$item['id']] = $item['fieldData']['slug'];
                    }
                }

                // Оновлюємо offset для наступного запиту
                $offset += 1;

                $total = $data['pagination']['total'];
            } else {

                Log::info("error webflow API: " . $response->body());
            }

        } while ($offset < $total);

        return $allItems;


    }

    private function publishItems($data)
    {
        sleep(2);
        $publishCollectionItemsURL = "https://api.webflow.com/v2/collections/66b5d2eb8c401c42d7d853f0/items/publish";

        $ItemsCollection = [];
        $items = $data;

        if(isset($data['items'])){
            $items = $items['items'];
            foreach ($items as $item){

                $ItemsCollection[] = $item['id'];
            }
        }

        $data = [
            "itemIds" => $ItemsCollection
        ];

        $webflowPublish = Http::withToken(config('services.webflow.api_key'))
            ->post($publishCollectionItemsURL, $data);

        if($webflowPublish->successful()){
            Log::info('Items p[ublished to Webflow successfully');
        }else{
            Log::info('Failed to publish items in Webflow'. $webflowPublish->body());
        }
    }
}
