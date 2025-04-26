<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct()
    {
        $this->apiKey = config('services.openweathermap.key');
    }

    public function getWeatherData($city)
    {
        // Get current weather
        $currentWeather = $this->getCurrentWeather($city);
        
        // Get 5-day forecast
        $forecast = $this->getForecast($city);

        return [
            'current' => $currentWeather,
            'forecast' => $forecast,
            'city_name' => $city
        ];
    }

    public function getWeatherDataByCoordinates($lat, $lon)
    {
        // Get current weather
        $currentWeather = $this->getCurrentWeatherByCoordinates($lat, $lon);
        
        // Get 5-day forecast
        $forecast = $this->getForecastByCoordinates($lat, $lon);

        // Get city name from the current weather response
        $response = Http::get("{$this->baseUrl}/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apiKey,
            'units' => 'metric'
        ]);

        $cityName = 'Current Location';
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['name'])) {
                $cityName = 'Current Location (' . $data['name'] . ')';
            }
        }

        return [
            'current' => $currentWeather,
            'forecast' => $forecast,
            'city_name' => $cityName
        ];
    }

    protected function getCurrentWeather($city)
    {
        $response = Http::get("{$this->baseUrl}/weather", [
            'q' => $city,
            'appid' => $this->apiKey,
            'units' => 'metric'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $date = now();
            return [
                'temperature' => $data['main']['temp'],
                'humidity' => $data['main']['humidity'],
                'wind_speed' => round($data['wind']['speed'] * 3.6, 1),
                'rain_probability' => $this->calculateRainProbability($data),
                'date' => $date->format('d.m.Y'),
                'day_name' => $date->format('l'),
                'condition' => $this->getWeatherCondition($data)
            ];
        }

        return null;
    }

    protected function getCurrentWeatherByCoordinates($lat, $lon)
    {
        $response = Http::get("{$this->baseUrl}/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apiKey,
            'units' => 'metric'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $date = now();
            return [
                'temperature' => $data['main']['temp'],
                'humidity' => $data['main']['humidity'],
                'wind_speed' => round($data['wind']['speed'] * 3.6, 1),
                'rain_probability' => $this->calculateRainProbability($data),
                'date' => $date->format('d.m.Y'),
                'day_name' => $date->format('l'),
                'condition' => $this->getWeatherCondition($data)
            ];
        }

        return null;
    }

    protected function getForecast($city)
    {
        $response = Http::get("{$this->baseUrl}/forecast", [
            'q' => $city,
            'appid' => $this->apiKey,
            'units' => 'metric'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Log::info('Full API Response', ['data' => $data]);
            
            $forecast = [];
            $today = now()->format('Y-m-d');
            
            // Group by day and calculate averages
            $dailyData = [];
            foreach ($data['list'] as $item) {
                $date = date('Y-m-d', $item['dt']);
                if ($date === $today) {
                    continue;
                }
                
                if (!isset($dailyData[$date])) {
                    $dailyData[$date] = [
                        'temperatures' => [],
                        'humidities' => [],
                        'wind_speeds' => [],
                        'pop_values' => [],
                        'weather_conditions' => [],
                        'date' => $date
                    ];
                }
                
                // Log detailed weather data for debugging
                Log::info('Weather item data', [
                    'date' => $date,
                    'time' => date('H:i', $item['dt']),
                    'pop' => $item['pop'] ?? 'not set',
                    'rain' => $item['rain'] ?? 'not set',
                    'weather' => $item['weather'][0]['main'] ?? 'not set',
                    'weather_description' => $item['weather'][0]['description'] ?? 'not set'
                ]);
                
                $dailyData[$date]['temperatures'][] = $item['main']['temp'];
                $dailyData[$date]['humidities'][] = $item['main']['humidity'];
                $dailyData[$date]['wind_speeds'][] = $item['wind']['speed'];
                $dailyData[$date]['pop_values'][] = $item['pop'] ?? 0;
                $dailyData[$date]['weather_conditions'][] = $item['weather'][0]['main'] ?? 'clear';
            }
            
            // Calculate averages and create forecast entries
            foreach ($dailyData as $date => $dayData) {
                $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
                
                // Calculate average POP for the day
                $avgPop = array_sum($dayData['pop_values']) / count($dayData['pop_values']);
                
                // Get the most common weather condition
                $weatherCounts = array_count_values($dayData['weather_conditions']);
                arsort($weatherCounts);
                $dominantWeather = key($weatherCounts);
                
                // Calculate final rain probability
                $rainProbability = $this->calculateFinalRainProbability($avgPop, $dominantWeather);
                
                Log::info('Daily forecast calculation', [
                    'date' => $date,
                    'avg_pop' => $avgPop,
                    'dominant_weather' => $dominantWeather,
                    'final_probability' => $rainProbability
                ]);
                
                $forecast[$date] = [
                    'temperature' => round(array_sum($dayData['temperatures']) / count($dayData['temperatures']), 1),
                    'humidity' => round(array_sum($dayData['humidities']) / count($dayData['humidities']), 1),
                    'wind_speed' => round(array_sum($dayData['wind_speeds']) / count($dayData['wind_speeds']) * 3.6, 1),
                    'rain_probability' => round($rainProbability, 1),
                    'date' => $dateObj->format('d.m.Y'),
                    'day_name' => $dateObj->format('l'),
                    'condition' => $this->getWeatherCondition(['weather' => [['main' => $dominantWeather]]])
                ];
            }

            return array_values($forecast);
        }

        return [];
    }

    protected function getForecastByCoordinates($lat, $lon)
    {
        $response = Http::get("{$this->baseUrl}/forecast", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apiKey,
            'units' => 'metric'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Log::info('Full API Response', ['data' => $data]);
            
            $forecast = [];
            $today = now()->format('Y-m-d');
            
            // Group by day and calculate averages
            $dailyData = [];
            foreach ($data['list'] as $item) {
                $date = date('Y-m-d', $item['dt']);
                if ($date === $today) {
                    continue;
                }
                
                if (!isset($dailyData[$date])) {
                    $dailyData[$date] = [
                        'temperatures' => [],
                        'humidities' => [],
                        'wind_speeds' => [],
                        'pop_values' => [],
                        'weather_conditions' => [],
                        'date' => $date
                    ];
                }
                
                $dailyData[$date]['temperatures'][] = $item['main']['temp'];
                $dailyData[$date]['humidities'][] = $item['main']['humidity'];
                $dailyData[$date]['wind_speeds'][] = $item['wind']['speed'];
                $dailyData[$date]['pop_values'][] = $item['pop'] ?? 0;
                $dailyData[$date]['weather_conditions'][] = $item['weather'][0]['main'] ?? 'clear';
            }
            
            // Calculate averages and create forecast entries
            foreach ($dailyData as $date => $dayData) {
                $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
                
                $avgPop = array_sum($dayData['pop_values']) / count($dayData['pop_values']);
                
                $weatherCounts = array_count_values($dayData['weather_conditions']);
                arsort($weatherCounts);
                $dominantWeather = key($weatherCounts);
                
                $rainProbability = $this->calculateFinalRainProbability($avgPop, $dominantWeather);
                
                $forecast[$date] = [
                    'temperature' => round(array_sum($dayData['temperatures']) / count($dayData['temperatures']), 1),
                    'humidity' => round(array_sum($dayData['humidities']) / count($dayData['humidities']), 1),
                    'wind_speed' => round(array_sum($dayData['wind_speeds']) / count($dayData['wind_speeds']) * 3.6, 1),
                    'rain_probability' => round($rainProbability, 1),
                    'date' => $dateObj->format('d.m.Y'),
                    'day_name' => $dateObj->format('l'),
                    'condition' => $this->getWeatherCondition(['weather' => [['main' => $dominantWeather]]])
                ];
            }

            return array_values($forecast);
        }

        return [];
    }

    protected function getWeatherCondition($data)
    {
        if (!isset($data['weather'][0]['main'])) {
            return 'clear';
        }

        $weather = strtolower($data['weather'][0]['main']);
        switch ($weather) {
            case 'rain':
            case 'drizzle':
                return 'rainy';
            case 'thunderstorm':
                return 'stormy';
            case 'snow':
                return 'snowy';
            case 'clouds':
                return 'cloudy';
            case 'mist':
            case 'fog':
                return 'foggy';
            case 'clear':
                return 'clear';
            default:
                return $weather;
        }
    }

    protected function calculateFinalRainProbability($avgPop, $dominantWeather)
    {
        // Convert POP to percentage
        $popPercentage = $avgPop * 100;
        
        // Get base probability from weather condition
        $weatherProbability = $this->getProbabilityFromWeather($dominantWeather);
        
        // If we have a significant POP value, use it
        if ($popPercentage >= 20) {
            return $popPercentage;
        }
        
        // Otherwise, use weather-based probability
        return $weatherProbability;
    }

    protected function getProbabilityFromWeather($weather)
    {
        $weather = strtolower($weather);
        switch ($weather) {
            case 'rain':
            case 'drizzle':
            case 'thunderstorm':
                return 80;
            case 'snow':
                return 60;
            case 'clouds':
                return 40;
            case 'mist':
            case 'fog':
                return 30;
            case 'clear':
                return 10;
            default:
                return 20;
        }
    }

    protected function calculateRainProbability($data)
    {
        // First try to get probability of precipitation (pop)
        if (isset($data['pop'])) {
            return $data['pop'] * 100;
        }
        
        // If no pop, check for rain volume
        if (isset($data['rain'])) {
            $rainVolume = $data['rain']['1h'] ?? $data['rain']['3h'] ?? 0;
            if ($rainVolume > 0) {
                return min($rainVolume * 20, 100);
            }
        }
        
        // If no rain data at all, check weather condition
        if (isset($data['weather'][0]['main'])) {
            return $this->getProbabilityFromWeather(strtolower($data['weather'][0]['main']));
        }
        
        return 10;
    }
} 