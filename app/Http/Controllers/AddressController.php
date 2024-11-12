<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function translate(Request $request)
    {
        $address = $request->input('address');
        $apiKey = env('OPENAI_API_KEY');
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo', // Using a ChatGPT model
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "
                        translate the following Arabic address into English, ensuring that it follows standard Google Maps address formatting. Translate 'حي' as 'District' or 'Dist.' and 'شارع' as 'Street' or 'St.' Keep proper names and city names unchanged. If the text is already in English, leave it unchanged. Do not provide comments or extra responses.if the address is  incomplete or contain a typing error, tranitlate it. Use the format: Street, District, House number, if these data a available:
                        $address"
                    ],
                ],
            ],
        ]);
        $body = json_decode($response->getBody(), true);
        $transliteration = $body['choices'][0]['message']['content'] ?? '';
        return response()->json(['translation' => trim($transliteration)]);
    }
}
