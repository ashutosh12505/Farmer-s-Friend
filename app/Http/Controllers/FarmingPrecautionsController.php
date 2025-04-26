<?php

namespace App\Http\Controllers;

use App\Services\FarmingPrecautionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FarmingPrecautionsController extends Controller
{
    protected $farmingPrecautionsService;

    public function __construct(FarmingPrecautionsService $farmingPrecautionsService)
    {
        $this->farmingPrecautionsService = $farmingPrecautionsService;
    }

    public function index()
    {
        return view('farming-precautions');
    }

    public function getPrecautions(Request $request)
    {
        try {
            $lat = $request->input('lat');
            $lon = $request->input('lon');
            $crop = $request->input('crop');

            // Temporary debug code
            Log::info('API Key Check', [
                'config_key' => config('services.weatherapi.key'),
                'env_key' => env('WEATHERAPI_KEY')
            ]);

            $weatherData = $this->farmingPrecautionsService->getCurrentWeather($lat, $lon);
            
            if (isset($weatherData['error'])) {
                return redirect()->route('farming-precautions')
                    ->with('error', $weatherData['error']);
            }

            $precautions = $this->farmingPrecautionsService->getCropPrecautions(
                $crop,
                $weatherData['temperature'],
                $weatherData['humidity']
            );

            return view('farming-precautions', [
                'weather' => $weatherData,
                'crop' => $crop,
                'precautions' => $precautions
            ]);
        } catch (\Exception $e) {
            return redirect()->route('farming-precautions')
                ->with('error', 'Error fetching precautions: ' . $e->getMessage());
        }
    }
} 