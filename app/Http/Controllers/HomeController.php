<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $logStatus = $this->getTimeLogStatus($request);
        return view('logs.log_time', compact('logStatus'));
    }

    public function logs()
    {
        return view('logs.index');
    }

    public function evaluation()
    {
        return view('logs.evaluation');
    }

    private function getTimeLogStatus(Request $request): JsonResponse
    {
        $client = new Client();

        $user = $request->user();
        $bearerToken = $user->currentAccessToken()->token;

        try {
            $apiUrl = route('time-log.status');

            $response = $client->get($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return response()->json(['message' => 'API request failed', 'error' => $e->getMessage()], 500);
        }
    }
}
