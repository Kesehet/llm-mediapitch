<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NgrokService
{
    public function getTunnels()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('NGROK_API_KEY'),
            'Content-Type' => 'application/json',
            'Ngrok-Version' => '2',
        ])->get('https://api.ngrok.com/tunnels');

        if ($response->failed()) {
            Log::error("Error fetching tunnels: " . $response->body());
            throw new \Exception("Error fetching tunnels.");
        }

        $data = $response->json();

        return collect($data['tunnels'] ?? [])->pluck('public_url');
    }
}