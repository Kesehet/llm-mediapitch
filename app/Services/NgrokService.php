<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NgrokService
{
    public function getTunnels()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('NGROK_API_KEY'),
            'Content-Type' => 'application/json',
            'Ngrok-Version' => '2',
        ])->get('https://api.ngrok.com/tunnels');

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['tunnels'])) {
                return collect($data['tunnels'])->pluck('public_url');
            }
        }

        throw new \Exception("No tunnels found or error in fetching tunnels.");
    }
}
