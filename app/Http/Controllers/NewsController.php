<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function search(Request $request)
    {
        $q   = $request->input('q', 'logistics');
        $max = min((int) $request->input('max', 10), 20); // cap at 20
        $from = $request->input('from');

        $apiKey = config('services.gnews.key', '');

        if (!$apiKey) {
            return response()->json(['articles' => []]);
        }

        $params = [
            'q'      => $q,
            'lang'   => 'en',
            'max'    => $max,
            'sortby' => 'publishedAt',
            'apikey' => $apiKey,
        ];

        if ($from) {
            $params['from'] = $from;
        }

        try {
            $response = Http::timeout(15)->get('https://gnews.io/api/v4/search', $params);

            if (!$response->ok()) {
                return response()->json(['articles' => []]);
            }

            $data = $response->json();

            $articles = collect($data['articles'] ?? [])->map(fn($a) => [
                'title'       => $a['title']              ?? '',
                'description' => $a['description']        ?? '',
                'source'      => ['name' => $a['source']['name'] ?? '—'],
                'url'         => $a['url']                ?? '',
                'image'       => $a['image']              ?? null,
                'publishedAt' => $a['publishedAt']        ?? null,
            ])->values();

            return response()->json(['articles' => $articles]);

        } catch (\Exception $e) {
            return response()->json(['articles' => []]);
        }
    }
}
