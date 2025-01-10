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

        if (!isset($collections['collections']) || empty($collections['collections'])) {

        }

        $categoryCollection = collect($collections['collections'])
            ->firstWhere('slug', $slug); // Знайти колекцію категорій

        //var_dump($categoryCollection);
        if (!$categoryCollection) {

        }


        $items = $this->webflowApi->getItems($categoryCollection['id']);

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
