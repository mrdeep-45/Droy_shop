<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class WhatsappController extends Controller
{
    //
     private string $baseUrl;
    private int $timeout;
    private string $accessToken;
    private string $instanceId;

    public function __construct()
    {
        $config = DB::table('wa_config')->first();

        $this->baseUrl = 'https://vferp.com/eizap/v1';
        $this->timeout = 30;

        if ($config) {
            $this->accessToken = $config->api_key;
            $this->instanceId = $config->instance_id;
        }
    }

    private function makeRequest(string $method, $endpoint, $query = [])
    {
        try {
            $url = $this->baseUrl . $endpoint;

            $response = Http::timeout($this->timeout)
                        ->acceptJson()
                ->$method($url, $query);

            return $response->json();
        } catch (\Exception $e) {

            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    public function createSession()
    {
        $response = $this->makeRequest('get', '/create', [
            'access_token' => $this->accessToken
        ]);

        if ($response['success'] && isset($response['data']['instance_id'])) {
            $instanceId = $response['data']['instance_id'];

            DB::table('wa_config')->updateOrInsert(
                ['id' => 1],
                ['instance_id' => $instanceId]
            );

            $this->instanceId = $instanceId;

        }

        return response()->json($response);
    }

    public function getStatus()
    {
        $query = ['access_token' => $this->accessToken];

        if (!$this->instanceId) {
            $this->createSession();
        }

        $query['instance_id'] = $this->instanceId;

        return $this->makeRequest('get', '/status', $query);
    }


    public function sendText(string $number, string $message)
    {
        return $this->makeRequest('get', '/send', [
            'access_token' => $this->accessToken,
            'instance_id' => $this->instanceId,
            'number' => $number,
            'type' => 'text',
            'message' => $message
        ]);
    }

    public function sendMedia(string $number, string $message, string $mediaUrl, string $filename)
    {
        return $this->makeRequest('get', '/send', [
            'access_token' => $this->accessToken,
            'instance_id' => $this->instanceId,
            'number' => $number,
            'type' => 'media',
            'message' => $message,
            'media_url' => $mediaUrl,
            'filename' => $filename
        ]);
    }

    public function getWAInstatnce()
    {
        $config = DB::table('wa_config')->first();
        return response()->json($config);
    }
}
