<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenWeatherMapService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct()
    {
        $this->apiKey = config('services.openweathermap.key');
    }

    public function getCurrentWeather($lat, $lon)
    {
        try {
            $response = Http::get("{$this->baseUrl}/weather", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'temperature' => $data['main']['temp'],
                    'humidity' => $data['main']['humidity'],
                    'location' => $data['name'] ?? 'Current Location'
                ];
            }

            Log::error('OpenWeatherMap API Error Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'error' => 'Failed to fetch weather data'
            ];
        } catch (\Exception $e) {
            Log::error('OpenWeatherMap API Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'error' => 'Error fetching weather data: ' . $e->getMessage()
            ];
        }
    }

    public function getWeatherByCity($city)
    {
        try {
            $response = Http::get("{$this->baseUrl}/weather", [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'temperature' => $data['main']['temp'],
                    'humidity' => $data['main']['humidity'],
                    'location' => $data['name'] ?? $city
                ];
            }

            Log::error('OpenWeatherMap API Error Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'error' => 'Failed to fetch weather data for the specified city'
            ];
        } catch (\Exception $e) {
            Log::error('OpenWeatherMap API Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'error' => 'Error fetching weather data: ' . $e->getMessage()
            ];
        }
    }
} 