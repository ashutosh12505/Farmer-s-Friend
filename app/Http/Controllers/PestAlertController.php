<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PestAlertController extends Controller
{
    public function index()
    {
        return view('pest-alerts');
    }

    public function getPestAlerts(Request $request)
    {
        try {
            $crop = $request->input('crop');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            if (!$crop || !$latitude || !$longitude) {
                return response()->json([
                    'error' => 'Missing required parameters'
                ], 400);
            }

            // Get weather data from OpenWeatherMap API
            $openWeatherResponse = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'lat' => $latitude,
                'lon' => $longitude,
                'appid' => config('services.openweathermap.key'),
                'units' => 'metric'
            ]);

            if (!$openWeatherResponse->successful()) {
                Log::error('OpenWeatherMap API error', [
                    'status' => $openWeatherResponse->status(),
                    'response' => $openWeatherResponse->json()
                ]);
                return response()->json([
                    'error' => 'Failed to fetch weather data'
                ], 500);
            }

            $weatherData = $openWeatherResponse->json();
            
            if (!isset($weatherData['main'])) {
                Log::error('Invalid OpenWeatherMap API response', ['response' => $weatherData]);
                return response()->json([
                    'error' => 'Invalid weather data received'
                ], 500);
            }

            $weather = [
                'temp' => $weatherData['main']['temp'],
                'humidity' => $weatherData['main']['humidity'],
                'rain' => $weatherData['rain']['1h'] ?? 0
            ];

            $pestAlert = $this->checkPestRisk($crop, $weather);

            return response()->json([
                'weather' => $weather,
                'pestAlert' => $pestAlert
            ]);
        } catch (\Exception $e) {
            Log::error('Pest alert error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'An error occurred while processing your request'
            ], 500);
        }
    }

    private function checkPestRisk($crop, $weather)
    {
        switch ($crop) {
            case "Bajra":
                if ($weather['humidity'] > 70) {
                    return "⚠️ Risk of Downy Mildew. Use resistant varieties and apply fungicides.";
                } else if ($weather['temp'] > 35) {
                    return "⚠️ Risk of Shoot Fly. Monitor for deadhearts and apply insecticides.";
                }
                return "✅ No major pest risks detected for Bajra.";

            case "Barley":
                if ($weather['humidity'] > 60 && $weather['temp'] > 20) {
                    return "⚠️ Risk of Rust Disease. Inspect leaves and apply fungicide early.";
                } else if ($weather['temp'] < 15 && $weather['humidity'] > 50) {
                    return "⚠️ Risk of Powdery Mildew. Maintain field hygiene.";
                }
                return "✅ No major pest risks detected for Barley.";

            case "Cardamom":
                if ($weather['humidity'] > 75) {
                    return "⚠️ Risk of Rhizome Rot. Improve drainage and use fungicides.";
                } else if ($weather['temp'] > 30) {
                    return "⚠️ Risk of Shoot Borer. Remove affected shoots and apply insecticides.";
                }
                return "✅ No major pest risks detected for Cardamom.";

            case "Chillies":
                if ($weather['humidity'] > 65) {
                    return "⚠️ Risk of Fruit Rot. Ensure proper spacing and ventilation.";
                } else if ($weather['temp'] > 30) {
                    return "⚠️ Risk of Thrips. Monitor flowers and apply insecticides.";
                }
                return "✅ No major pest risks detected for Chillies.";

            case "Coffee":
                if ($weather['humidity'] > 80) {
                    return "⚠️ Risk of Coffee Leaf Rust. Prune affected branches and apply fungicides.";
                } else if ($weather['temp'] > 25) {
                    return "⚠️ Risk of Coffee Berry Borer. Monitor berries and use traps.";
                }
                return "✅ No major pest risks detected for Coffee.";

            case "Cotton":
                if ($weather['temp'] > 30 && $weather['humidity'] < 30) {
                    return "⚠️ Risk of Whitefly attack. Monitor leaves and use neem oil spray.";
                } else if ($weather['humidity'] > 65) {
                    return "⚠️ Risk of Bollworm. Monitor buds and flowers.";
                }
                return "✅ No major pest risks detected for Cotton.";

            case "Groundnut":
                if ($weather['humidity'] > 60 && $weather['temp'] > 25) {
                    return "⚠️ Risk of Leaf Spot Disease. Use resistant varieties and fungicides.";
                } else if ($weather['temp'] > 30) {
                    return "⚠️ Risk of Aphid attack. Monitor underside of leaves.";
                }
                return "✅ No major pest risks detected for Groundnut.";

            case "Maize":
                if ($weather['humidity'] > 60) {
                    return "⚠️ Risk of Leaf Blight. Apply protective fungicides.";
                } else if ($weather['temp'] > 28) {
                    return "⚠️ Risk of Fall Armyworm. Early scouting recommended.";
                }
                return "✅ No major pest risks detected for Maize.";

            case "Mustard":
                if ($weather['humidity'] > 70) {
                    return "⚠️ Risk of White Rust. Remove infected plants and apply fungicides.";
                } else if ($weather['temp'] > 28) {
                    return "⚠️ Risk of Aphid attack. Use yellow sticky traps and insecticides.";
                }
                return "✅ No major pest risks detected for Mustard.";

            case "Onion":
                if ($weather['humidity'] > 70) {
                    return "⚠️ Risk of Purple Blotch. Apply protective fungicides.";
                } else if ($weather['temp'] > 30) {
                    return "⚠️ Risk of Thrips. Monitor leaves and apply insecticides.";
                }
                return "✅ No major pest risks detected for Onion.";

            case "Potato":
                if ($weather['humidity'] > 70) {
                    return "⚠️ Risk of Late Blight. Apply protective fungicides and ensure good ventilation.";
                } else if ($weather['temp'] > 25) {
                    return "⚠️ Risk of Colorado Potato Beetle. Monitor for egg masses and larvae.";
                }
                return "✅ No major pest risks detected for Potato.";

            case "Pulses":
                if ($weather['humidity'] > 65) {
                    return "⚠️ Risk of Rust Disease. Apply fungicides at early stages.";
                } else if ($weather['temp'] > 30) {
                    return "⚠️ Risk of Pod Borer. Monitor for eggs and larvae.";
                }
                return "✅ No major pest risks detected for Pulses.";

            case "Rice":
                if ($weather['humidity'] > 70 && $weather['rain'] > 5) {
                    return "⚠️ Risk of Brown Plant Hopper. Ensure proper drainage and use preventive pesticides.";
                } else if ($weather['temp'] > 30) {
                    return "⚠️ Risk of Stem Borer. Monitor for deadhearts and apply management measures.";
                }
                return "✅ No major pest risks detected for Rice.";

            case "Sorghum":
                if ($weather['humidity'] > 65) {
                    return "⚠️ Risk of Grain Mold. Harvest at proper maturity.";
                } else if ($weather['temp'] > 32) {
                    return "⚠️ Risk of Shoot Fly. Monitor for deadhearts.";
                }
                return "✅ No major pest risks detected for Sorghum.";

            case "Soybean":
                if ($weather['humidity'] > 65) {
                    return "⚠️ Risk of Rust Disease. Apply fungicides at early stages.";
                } else if ($weather['temp'] > 30) {
                    return "⚠️ Risk of Soybean Aphid. Monitor plant growth and apply insecticides if needed.";
                }
                return "✅ No major pest risks detected for Soybean.";

            case "Sugarcane":
                if ($weather['temp'] > 28 && $weather['humidity'] > 60) {
                    return "⚠️ Risk of Early Shoot Borer. Monitor young shoots and apply pesticides.";
                }
                return "✅ No major pest risks detected for Sugarcane.";

            case "Tea":
                if ($weather['humidity'] > 65) {
                    return "⚠️ Risk of Red Spider Mite. Maintain shade and moisture.";
                }
                return "✅ No major pest risks detected for Tea.";

            case "Tomato":
                if ($weather['humidity'] > 60) {
                    return "⚠️ Risk of Early Blight. Remove affected leaves and apply fungicides.";
                } else if ($weather['temp'] > 28) {
                    return "⚠️ Risk of Fruit Borer. Use pheromone traps.";
                }
                return "✅ No major pest risks detected for Tomato.";

            case "Turmeric":
                if ($weather['humidity'] > 75) {
                    return "⚠️ Risk of Rhizome Rot. Improve drainage and use fungicides.";
                } else if ($weather['temp'] > 30) {
                    return "⚠️ Risk of Shoot Borer. Remove affected shoots and apply insecticides.";
                }
                return "✅ No major pest risks detected for Turmeric.";

            case "Wheat":
                if ($weather['humidity'] > 60 && $weather['temp'] > 20) {
                    return "⚠️ Risk of Rust Disease. Inspect leaves and apply fungicide early.";
                } else if ($weather['temp'] < 15 && $weather['humidity'] > 50) {
                    return "⚠️ Risk of Powdery Mildew. Maintain field hygiene.";
                }
                return "✅ No major pest risks detected for Wheat.";

            default:
                return "ℹ️ Pest alert data not available for this crop yet.";
        }
    }
} 