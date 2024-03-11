<?php

namespace App\Http\Controllers;

use App\Services\NgrokService;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedForCredentialsHttpException;
// add request 
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        
    }

    public function index()
    {
        return (new NgrokService())->getTunnels();
    }

    public function llm(Request $request)
    {
        try {
            $urls = $this->index();
            $firstUrl = $urls->first();
            if (!$firstUrl) {
                return response()->json(['error' => 'No tunnels found or error in fetching tunnels.'], 400);
            }

            $client = new Client(); // Create a Guzzle HTTP client instance
            $response = $client->post($firstUrl . "/llm", [
                'form_params' => [
                    'query' => $request->input('query'),
                ],
            ]);

            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}