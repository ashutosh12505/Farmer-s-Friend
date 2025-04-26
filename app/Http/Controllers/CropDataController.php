<?php

namespace App\Http\Controllers;

use App\Models\CropData;
use App\Services\WeatherService;
use Illuminate\Http\Request;

class CropDataController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index()
    {
        return view('welcome');
    }

    public function history()
    {
        $data = CropData::latest()->get();
        return view('history', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_input' => 'required|string'
        ]);

        $city = $request->user_input;
        $weatherData = $this->weatherService->getWeatherData($city);

        if (!$weatherData['current']) {
            return redirect()->back()->with('error', 'City not found. Please enter a valid city name.');
        }

        return view('welcome', compact('weatherData'));
    }
}
