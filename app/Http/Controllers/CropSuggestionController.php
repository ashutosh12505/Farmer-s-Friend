<?php

namespace App\Http\Controllers;

use App\Services\OpenWeatherMapService;
use Illuminate\Http\Request;

class CropSuggestionController extends Controller
{
    protected $openWeatherMapService;
    protected $cropRecommendations;

    public function __construct(OpenWeatherMapService $openWeatherMapService)
    {
        $this->openWeatherMapService = $openWeatherMapService;
        $this->cropRecommendations = [
            ["name" => "Bajra", "tempRange" => [25, 45], "humidityRange" => [5, 50]],
            ["name" => "Barley", "tempRange" => [12, 35], "humidityRange" => [10, 60]],
            ["name" => "Cardamom", "tempRange" => [10, 35], "humidityRange" => [20, 60]],
            ["name" => "Chillies", "tempRange" => [20, 40], "humidityRange" => [10, 60]],
            ["name" => "Coffee", "tempRange" => [15, 35], "humidityRange" => [15, 70]],
            ["name" => "Cotton", "tempRange" => [21, 40], "humidityRange" => [10, 60]],
            ["name" => "Groundnut", "tempRange" => [25, 45], "humidityRange" => [10, 60]],
            ["name" => "Maize", "tempRange" => [18, 30], "humidityRange" => [10, 60]],
            ["name" => "Mustard", "tempRange" => [10, 35], "humidityRange" => [5, 50]],
            ["name" => "Onion", "tempRange" => [13, 35], "humidityRange" => [10, 60]],
            ["name" => "Potato", "tempRange" => [15, 30], "humidityRange" => [15, 70]],
            ["name" => "Pulses", "tempRange" => [18, 35], "humidityRange" => [5, 50]],
            ["name" => "Rice", "tempRange" => [20, 45], "humidityRange" => [20, 70]],
            ["name" => "Sorghum", "tempRange" => [25, 40], "humidityRange" => [5, 50]],
            ["name" => "Soybean", "tempRange" => [20, 40], "humidityRange" => [10, 60]],
            ["name" => "Sugarcane", "tempRange" => [21, 45], "humidityRange" => [15, 70]],
            ["name" => "Tea", "tempRange" => [13, 35], "humidityRange" => [15, 70]],
            ["name" => "Tomato", "tempRange" => [20, 35], "humidityRange" => [10, 60]],
            ["name" => "Turmeric", "tempRange" => [20, 40], "humidityRange" => [20, 70]],
            ["name" => "Wheat", "tempRange" => [12, 35], "humidityRange" => [15, 60]]
        ];
    }

    public function index()
    {
        return view('crop-suggestion');
    }

    public function getSuggestions(Request $request)
    {
        try {
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            $city = $request->input('city');

            if ($city) {
                $weatherData = $this->openWeatherMapService->getWeatherByCity($city);
            } else {
                $weatherData = $this->openWeatherMapService->getCurrentWeather($lat, $lon);
            }
            
            if (isset($weatherData['error'])) {
                return redirect()->route('crop-suggestion')
                    ->with('error', $weatherData['error']);
            }

            $temperature = $weatherData['temperature'];
            $humidity = $weatherData['humidity'];

            $suggestedCrops = [];
            $droughtTolerantCrops = [
                ["name" => "Bajra (Pearl Millet)", "notes" => "Grows very well in dry, sandy soils"],
                ["name" => "Sorghum (Jowar)", "notes" => "Excellent drought resistance"],
                ["name" => "Pulses (Gram, Moong, Arhar)", "notes" => "Needs very little water"],
                ["name" => "Mustard", "notes" => "Tolerates dry and semi-dry conditions"],
                ["name" => "Groundnut", "notes" => "Can survive short dry periods"],
                ["name" => "Castor", "notes" => "Highly drought resistant"],
                ["name" => "Guar (Cluster Bean)", "notes" => "Very hardy, low water crop"]
            ];

            if ($humidity <= 5) {
                return view('crop-suggestion', [
                    'weather' => $weatherData,
                    'droughtTolerantCrops' => $droughtTolerantCrops,
                    'isLowHumidity' => true
                ]);
            }

            foreach ($this->cropRecommendations as $crop) {
                if ($temperature >= $crop['tempRange'][0] && 
                    $temperature <= $crop['tempRange'][1] && 
                    $humidity >= $crop['humidityRange'][0] && 
                    $humidity <= $crop['humidityRange'][1]) {
                    $suggestedCrops[] = $crop;
                }
            }

            return view('crop-suggestion', [
                'weather' => $weatherData,
                'suggestedCrops' => $suggestedCrops,
                'isLowHumidity' => false
            ]);
        } catch (\Exception $e) {
            return redirect()->route('crop-suggestion')
                ->with('error', 'Error getting crop suggestions: ' . $e->getMessage());
        }
    }
} 