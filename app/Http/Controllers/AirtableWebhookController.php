<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;

class AirtableWebhookController extends Controller
{

    public function getUniversity(Request $request)
    {
        $airtableUrl = "https://api.airtable.com/v0/appKNOyMC0fPOLylY/Universities";
        $webflowUrl = "https://api.webflow.com/v2/collections/66b5d2eb8c401c42d7d853f0/items/bulk";


        $response = Http::withToken(config('services.airtable.api_key'))->get($airtableUrl);
        $records = $response->json()['records'];

        if (isset($records)) {
            foreach ($records as $record) {
                $fields = $record['fields'];


                if (isset($fields['Name']) && $fields['Name'] === "Manchester Metropolitan University") {

                    $description = $fields['Description'] ?? null;
                    $logotypeUrl = $fields['Logotype'][0]['url'] ?? null;
                    $living = $fields['Living and Accommodation'] ?? null;
                    $heroBanner = $fields['Hero Banner'][0]['url'] ?? null;

                    if ($description && $logotypeUrl) {

                        $converter = new CommonMarkConverter();
                        $text = $converter->convert($living)->getContent();
                        dd($text);

                        $data = [
                            "cmsLocaleId" => [],
                            "isArchived" => false,
                            "isDraft" => false,
                            "fieldData" => [
                                "name" => $fields['Name'],
                                'slug'=>'slug-name',
                                "description-2" => $description,
                                "logo" => $logotypeUrl,
                                "living"=>$living,
                                "hero-banner"=>$heroBanner
                            ]
                        ];

//                        dd($data);


                        $webflowResponse = Http::withToken(config('services.webflow.api_key'))
//                            ->withHeaders([
//                                'Content-Type' => 'application/json',
//                            ])
                            ->post($webflowUrl, $data);
                        dd($webflowResponse);

                        if ($webflowResponse->successful()) {
                            return response()->json(['message' => 'Data sent to Webflow successfully']);
                        } else {
                            return response()->json(['error' => 'Failed to send data to Webflow'], 500);
                        }
                    } else {
                        return response()->json(['error' => 'Required fields are missing in Airtable record'], 500);
                    }
                }
            }
        } else {
            return response()->json(['error' => 'No records found'], 404);
        }
    }


    public function universitiesUpdate(Request $request)
    {
//        Log::info($request);

        $macSecret = config('services.airtable.mac_secret'); // Збережіть ваш MAC секрет в конфігі
        $receivedMac = $request->header('X-Airtable-Content-MAC');
        Log::info($receivedMac);

        $computedMac = $this->computeMac($request->getContent(), $macSecret);
        Log::info($computedMac);

        if ($receivedMac !== $computedMac) {
            abort(403, 'MAC signature verification failed');
        }

        // Обробка повідомлення
        $data = $request->json()->all();
        if ($data) {
            Log::info($data);
            $this->listWebhookPayload($data['webhook']['id']);


            // Відповідь з 200 статусом
            return response()->noContent();
        }


        // Оновлення вебхука

    }

    private function computeMac($body, $macSecretBase64)
    {
        $macSecret = base64_decode($macSecretBase64);
        return 'hmac-sha256=' . hash_hmac('sha256', $body, $macSecret);
    }

    private function listWebhookPayload($webhookId)
    {
        $universityBase = 'appKNOyMC0fPOLylY';

        $url = "https://api.airtable.com/v0/bases/${universityBase}/webhooks/{$webhookId}/payloads";
//        $url = "https://api.airtable.com/v0/bases//refresh";

        $response = Http::withToken(config('services.airtable.api_key'))
            ->get($url);

        Log::info($response);

        if (!$response->successful()) {
            \Log::error("Failed to refresh Airtable webhook: " . $response->body());
        }
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

}
