<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticService
{
    protected string $measurementId;
    protected string $apiSecret;

    public function __construct()
    {
        // Read configuration from config
        $this->measurementId = config('services.analytics.measurementId');
        $this->apiSecret = config('services.analytics.apiSecret');
    }

    /**
     * Send events to GA4
     *
     * @param  string $userId  User identifier
     * @param  string $eventname Event name (e.g., signup)
     * @param  array  $params Event parameters
     * @return bool Success status of the request
     */
    public function sendGA4Event($userId, $eventname, $params): bool
    {
        // Get the current time as UNIX timestamp
        $timestamp = time(); // Time in UNIX timestamp format

        // Prepare the request payload
        $payload = [
            'client_id' => $userId, // Unique identifier for the user
            'events' => [
                [
                    'name' => $eventname, // Event name
                    'params' => array_merge($params, [
                        'timestamp' => $timestamp, // Adding the timestamp
                    ]),
                ]
            ]
        ];

        // Send the request to GA4
        $response = Http::withHeaders([
            'Content-Type' => 'application/json', // Adding the Content-Type header
        ])->post("https://www.google-analytics.com/mp/collect?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}", $payload);

        // Check if the request was successful
        if ($response->successful()) {
            return true; // Event successfully sent
        } else {
            // Log the error response for debugging
            Log::error('Google Analytics Event Error:', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false; // Event failed to send
        }
    }
}
