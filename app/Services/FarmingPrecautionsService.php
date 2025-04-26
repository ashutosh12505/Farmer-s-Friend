<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FarmingPrecautionsService
{
    protected $apiKey;
    protected $baseUrl = 'http://api.weatherapi.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.weatherapi.key');
    }

    public function getCurrentWeather($lat, $lon)
    {
        try {
            $url = "{$this->baseUrl}/current.json";
            $params = [
                'key' => $this->apiKey,
                'q' => "{$lat},{$lon}"
            ];

            // Log the request details
            Log::info('Weather API Request', [
                'url' => $url,
                'params' => array_merge($params, ['key' => 'REDACTED']), // Don't log the actual API key
                'api_key_length' => strlen($this->apiKey)
            ]);

            $response = Http::get($url, $params);

            // Log the response details
            Log::info('Weather API Response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'temperature' => $data['current']['temp_c'],
                    'humidity' => $data['current']['humidity'],
                    'location' => $data['location']['name'] ?? 'Current Location'
                ];
            }

            // Log the error response
            Log::error('Weather API Error Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'lat' => $lat,
                'lon' => $lon,
                'api_key_length' => strlen($this->apiKey)
            ]);

            return [
                'error' => 'Failed to fetch weather data. Please try again later.'
            ];
        } catch (\Exception $e) {
            Log::error('Weather API Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lat' => $lat,
                'lon' => $lon,
                'api_key_length' => strlen($this->apiKey)
            ]);
            return [
                'error' => 'Error fetching weather data: ' . $e->getMessage()
            ];
        }
    }

    public function getCropPrecautions($crop, $temperature, $humidity)
    {
        $precautions = [];

        switch ($crop) {
            case "bajra":
                if ($temperature > 40) {
                    $precautions[] = "High temperature may affect pollination. Provide light irrigation.";
                } else if ($humidity < 20) {
                    $precautions[] = "Very low humidity can cause drought stress. Increase irrigation frequency.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "barley":
                if ($temperature < 10) {
                    $precautions[] = "Low temperature may affect germination. Delay sowing if needed.";
                } else if ($humidity < 30) {
                    $precautions[] = "Dry air may stress barley crops. Consider supplemental irrigation.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "cardamom":
                if ($temperature < 15) {
                    $precautions[] = "Cold weather may slow growth. Provide shade and wind protection.";
                } else if ($humidity < 40) {
                    $precautions[] = "Low humidity stresses cardamom plants. Maintain soil moisture.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "chillies":
                if ($temperature < 20) {
                    $precautions[] = "Low temperature can cause poor flowering. Monitor plants closely.";
                } else if ($humidity < 30) {
                    $precautions[] = "Low humidity can cause flower drop. Mist plants lightly if possible.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "coffee":
                if ($temperature < 15) {
                    $precautions[] = "Cold conditions may stress coffee plants. Provide shelter.";
                } else if ($humidity < 50) {
                    $precautions[] = "Low humidity increases berry drop risk. Ensure regular irrigation.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "cotton":
                if ($temperature < 18) {
                    $precautions[] = "Low temperature can delay cotton germination. Monitor soil temperature.";
                } else if ($humidity < 20) {
                    $precautions[] = "Dry air can stress cotton crops. Irrigate adequately.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "groundnut":
                if ($temperature > 38) {
                    $precautions[] = "High temperature may reduce pod development. Provide timely irrigation.";
                } else if ($humidity < 25) {
                    $precautions[] = "Low humidity can cause poor peg penetration. Keep soil moist.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "maize":
                if ($temperature > 37) {
                    $precautions[] = "High temp can cause pollen sterility. Irrigate to cool down crops.";
                } else if ($humidity < 30) {
                    $precautions[] = "Dry air may increase water loss. Increase irrigation cycles.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "mustard":
                if ($temperature < 10) {
                    $precautions[] = "Cold temperatures can delay mustard flowering. Delay sowing.";
                } else if ($humidity < 25) {
                    $precautions[] = "Low humidity may stress young plants. Light irrigation recommended.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "onion":
                if ($temperature > 35) {
                    $precautions[] = "High temperature may reduce bulb formation. Provide irrigation.";
                } else if ($humidity < 30) {
                    $precautions[] = "Dry conditions can cause splitting. Monitor soil moisture.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "potato":
                if ($temperature > 30) {
                    $precautions[] = "High temperature affects tuber formation. Use mulching to lower soil temp.";
                } else if ($humidity < 35) {
                    $precautions[] = "Low humidity may cause poor tuber quality. Water regularly.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "pulses":
                if ($temperature < 15) {
                    $precautions[] = "Low temperature can affect germination. Delay sowing.";
                } else if ($humidity < 25) {
                    $precautions[] = "Dry conditions can cause flower drop. Water if soil dries.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "rice":
                if ($temperature < 20) {
                    $precautions[] = "Cold weather affects rice tillering. Maintain water levels properly.";
                } else if ($humidity < 40) {
                    $precautions[] = "Low humidity may cause water stress. Keep paddy flooded.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "sorghum":
                if ($temperature > 38) {
                    $precautions[] = "Heat stress may cause grain shrinkage. Ensure timely irrigation.";
                } else if ($humidity < 25) {
                    $precautions[] = "Low humidity may stunt growth. Increase irrigation.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "soybean":
                if ($temperature > 35) {
                    $precautions[] = "High temperature may affect pod setting. Provide light irrigation.";
                } else if ($humidity < 30) {
                    $precautions[] = "Dry air may cause flower abortion. Maintain good soil moisture.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "sugarcane":
                if ($temperature < 20) {
                    $precautions[] = "Low temperature slows sugarcane sprouting. Delay planting.";
                } else if ($humidity < 30) {
                    $precautions[] = "Dry conditions affect sugarcane growth. Irrigate adequately.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "tea":
                if ($temperature > 30) {
                    $precautions[] = "High temperature stresses tea plants. Increase irrigation frequency.";
                } else if ($humidity < 50) {
                    $precautions[] = "Low humidity stresses tea leaves. Mist plantations if possible.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "tomato":
                if ($temperature > 35) {
                    $precautions[] = "High temperature reduces fruit setting. Provide shade nets if needed.";
                } else if ($humidity < 30) {
                    $precautions[] = "Low humidity can cause blossom drop. Increase watering frequency.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "turmeric":
                if ($temperature < 20) {
                    $precautions[] = "Low temperature may slow rhizome growth. Maintain soil moisture.";
                } else if ($humidity < 40) {
                    $precautions[] = "Low humidity stresses turmeric crops. Keep soil moist.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            case "wheat":
                if ($temperature < 10) {
                    $precautions[] = "Cold stress may delay wheat tillering. Irrigate lightly during cold spells.";
                } else if ($humidity < 25) {
                    $precautions[] = "Low humidity may affect flowering. Monitor field moisture.";
                } else {
                    $precautions[] = "All is well ✅";
                }
                break;

            default:
                $precautions[] = "Please select a valid crop.";
                break;
        }

        return $precautions;
    }
} 