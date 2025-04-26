<?php

namespace App\Http\Controllers;

use App\Services\WeatherWarningService;
use Illuminate\Http\Request;

class WeatherWarningController extends Controller
{
    protected $weatherWarningService;

    public function __construct(WeatherWarningService $weatherWarningService)
    {
        $this->weatherWarningService = $weatherWarningService;
    }

    public function index()
    {
        return view('weather-warnings');
    }

    public function getWarnings(Request $request)
    {
        try {
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            
            $data = $this->weatherWarningService->getWeatherWarnings($lat, $lon);
            return view('weather-warnings', $data);
        } catch (\Exception $e) {
            return redirect()->route('weather-warnings')
                ->with('error', 'Error fetching weather warnings: ' . $e->getMessage());
        }
    }
} 