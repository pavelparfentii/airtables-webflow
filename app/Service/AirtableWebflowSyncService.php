<?php


namespace App\Service;


use App\Service\Mapper\CategoryMapper;
use App\Service\Webflow\WebflowApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;
use Tapp\Airtable\Facades\AirtableFacade as Airtable;

class AirtableWebflowSyncService
{
    protected $airtableTable;
    protected $webflowCollectionUrl;
    protected $converter;


    public function __construct(string $airtableTable, string $webflowCollectionUrl)
    {
//        parent::__construct();
        $this->airtableTable = $airtableTable;
        $this->webflowCollectionUrl = $webflowCollectionUrl;
        $this->converter = new CommonMarkConverter();

    }

    public function sync()
    {
//        $webflowApi = new WebflowApi();
//        $categoryMapper = new CategoryMapper($webflowApi);
//
//        $subcategory = 'Law'; // Назва категорії, яку потрібно знайти
//        $webflowCategoryId = $categoryMapper->mapCategory($subcategory);
//        dd($webflowCategoryId);




        $records = Airtable::table($this->airtableTable)->all();
       // dd($records);
        $webflowItems = $this->getWebflowItems($this->webflowCollectionUrl);
        //dd($webflowItems);
        $webflowSlugs = $this->getWebflowSlugs($webflowItems);

//        dd($webflowSlugs);

        $airtableSlugs = collect($records)->mapWithKeys(function ($record) {
            $fields = $record['fields'] ?? [];
            return [$this->slugify($fields['Slug'] ?? '') => true]; // Генеруємо slug із Name
        });

        //dd($airtableSlugs);

        $slugsToDelete = collect($webflowSlugs)->filter(function ($webflowId, $slug) use ($airtableSlugs) {
            return !isset($airtableSlugs[$slug]); // Якщо slug відсутній у Airtable, додаємо його до видалення
        });


        $itemsToUpdate = [];
        $itemsToCreate = [];
        //$itemsToDelete = [];

        foreach ($records as $record) {
            $fields = $record['fields'];
            if (empty($fields) || count($fields) <= 1) {
                continue;
            }

            $slug = $this->slugify($fields['Slug'] ?? '');

            $fieldData = $this->prepareFieldData($this->airtableTable, $fields);

            //dd($fieldData);

            if (isset($fields['last_modified']) && !empty($fields['last_modified'])) {
                $lastModified = $fields['last_modified'];
                $lastModifiedDate = Carbon::parse($lastModified);

            }

            //var_dump(isset($webflowSlugs[$slug]) ? $webflowSlugs[$slug] : null);
            if (isset($webflowSlugs[$slug]) && $lastModifiedDate->isSameDay(now())) {
//                var_dump($webflowSlugs[$slug]);
                $itemsToUpdate[] = $this->prepareUpdateData($webflowSlugs[$slug], $fieldData);
            } elseif(!isset($webflowSlugs[$slug])) {
                $itemsToCreate[] = $fieldData;
            }
        }

        $itemsToDelete = $slugsToDelete->toArray();

        //dd($itemsToUpdate);
        //dd($itemsToUpdate);
//        dd($itemsToCreate);

        $this->updateWebflowItems($itemsToUpdate);

        $this->createWebflowItems($itemsToCreate);

        $this->deleteWebflowItems($itemsToDelete);
    }

    protected function getWebflowItems($url)
    {
//        return Http::withToken(config('services.webflow.api_key'))
//                ->get($this->webflowCollectionUrl . "/items")
//                ->json()['items'] ?? [];

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

    protected function getWebflowSlugs($webflowItems): array
    {
//        dd($webflowItems);

        $webflowSlugs = [];
        foreach ($webflowItems as $key => $item) {
            $webflowSlugs[$item] = $key;
        }

        return $webflowSlugs;

//        $slugs = [];
//        foreach ($webflowItems as $item) {
//            $slugs[$item['slug']] = $item['_id'];
//        }
//        return $slugs;
    }

    protected function prepareFieldData(string $tableKey, array $fields): array
    {


        $config = config('airtable_webflow.' . $tableKey);

        if (!$config) {
            throw new \Exception("Configuration for table '$tableKey' not found.");
        }
        //dd($fields);
        $fieldData = [];
        foreach ($config['fields_mapping'] as $airtableField => $webflowField) {
            $airtableField = $airtableField ?? null;
            $fieldData[$webflowField] = $fields[$airtableField] ?? null;
        }

        //dd($fieldData);

        foreach ($config['transformations'] as $field => $transformation) {
            if (in_array($field, ['category', 'training-center-relation'])) {
                $webflowApi = new WebflowApi();
                $categoryMapper = new CategoryMapper($webflowApi);

                // Apply transformation for 'category' and 'training-center-relation'
                $fieldData[$field] = $transformation($fields, $categoryMapper);

            } else {
                // Apply other transformations
                $fieldData[$field] = $transformation($fields, $this->converter ?? null);
            }
        }

        return array_filter($fieldData, fn($value) => !is_null($value));

    }

    protected function prepareUpdateData(string $webflowId, array $fieldData): array
    {
        return [
            'id' => $webflowId,
//            'isArchived' => false,
//            'isDraft' => false,
            'fieldData' => $fieldData,
        ];
    }

    protected function updateWebflowItems(array $items)
    {
        if (empty($items)) {
            return;
        }
        //dd($items);

        $webflowResponse = Http::withToken(config('services.webflow.api_key'))
            ->patch($this->webflowCollectionUrl, ['items' => $items]);

        if($webflowResponse->successful()){
            Log::info('Items updated in Webflow successfully');
        }else{
            Log::info('Failed to update items in Webflow'. $webflowResponse->body());
        }
    }

    protected function createWebflowItems(array $items)
    {
        if (empty($items)) {
            return;
        }

        $webflowResponse = Http::withToken(config('services.webflow.api_key'))
            ->post($this->webflowCollectionUrl . '/bulk', ['fieldData' => $items]);

        if($webflowResponse->successful()){
            Log::info('Items updated in Webflow successfully');
        }else{
            Log::info('Failed to create items in Webflow'. $webflowResponse->body());
        }

    }

    protected function deleteWebflowItems(array $items)
    {
        if (empty($items)) {
            return;
        }

        foreach ($items as $key=>$item){
            $webflowResponse =Http::withToken(config('services.webflow.api_key'))
                ->delete($this->webflowCollectionUrl . '/' . $item);
            if($webflowResponse->successful()){
                Log::info('Items removed in Webflow successfully');
            }else{
                Log::info('Failed to delete items in Webflow'. $webflowResponse->body());
            }
        }

    }

    protected function slugify(string $text): string
    {
        return Str::slug($text);
    }
}
