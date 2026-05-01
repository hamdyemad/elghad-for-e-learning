<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TlyncService
{
    protected $id;
    protected $token;
    protected $baseUrl;

    public function __construct()
    {
        $this->id = config('services.tlync.id');
        $this->token = config('services.tlync.token');
        $this->baseUrl = config('services.tlync.base_url');
    }

    /**
     * Initiate payment request to Tlync
     *
     * @param array $data
     * @return array
     */
    public function initiatePayment(array $data)
    {
        $payload = [
            'id' => $this->id,
            'amount' => $data['amount'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'backend_url' => $data['backend_url'],
            'frontend_url' => $data['frontend_url'],
            'custom_ref' => $data['custom_ref'],
        ];

        try {
            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                ])
                ->post($this->baseUrl . '/payment/initiate', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Tlync payment initiation failed', [
                'payload' => $payload,
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Tlync payment initiation exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'result' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
