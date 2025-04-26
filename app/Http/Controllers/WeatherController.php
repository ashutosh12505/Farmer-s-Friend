<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index()
    {
        return view('search');
    }

    public function search(Request $request)
    {
        $city = $request->input('user_input');
        
        try {
            $weatherData = $this->weatherService->getWeatherData($city);
            return view('search', compact('weatherData'));
        } catch (\Exception $e) {
            return redirect()->route('prediction')
                ->with('error', 'Error fetching weather data: ' . $e->getMessage());
        }
    }

    public function currentLocation(Request $request)
    {
        try {
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            
            $weatherData = $this->weatherService->getWeatherDataByCoordinates($lat, $lon);
            return view('search', compact('weatherData'));
        } catch (\Exception $e) {
            return redirect()->route('prediction')
                ->with('error', 'Error fetching weather data: ' . $e->getMessage());
        }
    }
} 