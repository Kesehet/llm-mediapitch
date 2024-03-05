<?php
use Illuminate\Http\Request;
use App\Services\NgrokService;

Route::get('tunnels', function (NgrokService $ngrokService) {
    try {
        $urls = $ngrokService->getTunnels();
        return response()->json($urls);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
})->name('tunnels');

Route::post('llm', function (Request $request, NgrokService $ngrokService) {
    try {
        $urls = $ngrokService->getTunnels();
        $firstUrl = $urls->first();
        if(!$firstUrl) {
            return response()->json(['error' => 'No tunnels found or error in fetching tunnels.'], 400);
        }
        $response = Http::asForm()->post($firstUrl, [
            'query' => $request->input('query'),
        ]);
        

        return $response->body();
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
})->name('llm');
