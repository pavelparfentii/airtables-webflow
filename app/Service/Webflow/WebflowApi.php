<?php


namespace App\Service\Webflow;


use Exception;
use Illuminate\Support\Facades\Http;

class WebflowApi
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.webflow.api_key'); // Дістаємо ключ API з конфігурації
    }

    public function getCollections(): array
    {
        return $this->makeRequest('GET', 'sites/664c7389e48704408a488d5c/collections');
    }

    public function getItems(string $collectionId): array
    {
        return $this->makeRequest('GET', "collections/{$collectionId}/items");
    }

    protected function makeRequest(string $method, string $endpoint): array
    {
        $url = "https://api.webflow.com/v2/{$endpoint}";
        $response = Http::withToken($this->apiKey)->{$method}($url);

        if (!$response->successful()) {
            throw new Exception("Webflow API Error: {$response->body()}");
        }

        return $response->json();
    }
}
