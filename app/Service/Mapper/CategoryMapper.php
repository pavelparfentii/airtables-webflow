<?php


namespace App\Service\Mapper;


use App\Service\Webflow\WebflowApi;

class CategoryMapper
{
    protected $webflowApi;

    // Кеш для збереження категорій
    protected $categoriesCache = null;

    public function __construct(WebflowApi $webflowApi)
    {
        $this->webflowApi = $webflowApi;
    }

    /**
     * Маппінг назви категорії на ID Webflow
     */
    public function mapCategory(?string $subcategory, string $slug): ?string
    {
        if (!$subcategory) {
            return null;
        }

        // Завантаження категорій, якщо кеш пустий
        if (!$this->categoriesCache) {
            $this->categoriesCache = $this->loadCategories($slug);
        }

        // Пошук категорії за назвою
        return $this->categoriesCache[$subcategory] ?? null;
    }

    /**
     * Завантаження всіх категорій із Webflow
     */


    protected function loadCategories(string $slug): array
    {
        $collections = $this->webflowApi->getCollections();
       // dd($collections);
        // Перевірка, чи є колекції у відповіді
        if (!isset($collections['collections']) || empty($collections['collections'])) {
           // throw new Exception('No collections found in Webflow API response.');
        }

        $categoryCollection = collect($collections['collections'])
            ->firstWhere('slug', $slug); // Знайти колекцію категорій

        //var_dump($categoryCollection);
        if (!$categoryCollection) {

            //throw new Exception('Category collection not found in Webflow.');
        }

        // Отримання всіх елементів категорій
        $items = $this->webflowApi->getItems($categoryCollection['id']);
        //dd($items);
        // Перевірка, чи є елементи у відповіді
        if (!isset($items['items']) || empty($items['items'])) {
           // throw new Exception('No items found in the category collection.');
        }

        return collect($items['items'])
            ->mapWithKeys(fn($item) => [
                $item['fieldData']['name'] => $item['id']
            ])
            ->toArray();
    }

}
