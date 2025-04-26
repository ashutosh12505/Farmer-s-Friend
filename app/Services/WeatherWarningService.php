<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherWarningService
{
    protected $apiKey;
    protected $baseUrl = 'http://api.weatherapi.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.weatherapi.key');
    }

    public function getWeatherWarnings($lat, $lon)
    {
        try {
            $response = Http::get("{$this->baseUrl}/forecast.json", [
                'key' => $this->apiKey,
                'q' => "{$lat},{$lon}",
                'alerts' => 'yes'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log the response for debugging
                Log::info('Weather API Response', ['data' => $data]);

                $warnings = [];
                if (isset($data['alerts']['alert'])) {
                    foreach ($data['alerts']['alert'] as $alert) {
                        $warnings[] = [
                            'headline' => $alert['headline'] ?? 'No headline available',
                            'severity' => $alert['severity'] ?? 'Unknown',
                            'urgency' => $alert['urgency'] ?? 'Unknown',
                            'areas' => $alert['areas'] ?? 'Not specified',
                            'description' => $alert['desc'] ?? 'No description available',
                            'instruction' => $alert['instruction'] ?? 'No instructions available',
                            'effective' => $alert['effective'] ?? 'Not specified',
                            'expires' => $alert['expires'] ?? 'Not specified'
                        ];
                    }
                }

                return [
                    'warnings' => $warnings,
                    'location' => $data['location']['name'] ?? 'Current Location'
                ];
            }

            return [
                'warnings' => [],
                'location' => 'Current Location',
                'error' => 'Failed to fetch weather warnings'
            ];
        } catch (\Exception $e) {
            Log::error('Weather API Error', ['error' => $e->getMessage()]);
            return [
                'warnings' => [],
                'location' => 'Current Location',
                'error' => 'Error fetching weather warnings: ' . $e->getMessage()
            ];
        }
    }
} 