<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CropDataController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\MarketPriceController;
use App\Http\Controllers\WeatherWarningController;
use App\Http\Controllers\FarmingPrecautionsController;
use App\Http\Controllers\CropSuggestionController;
use App\Http\Controllers\PestAlertController;

// Home page route
Route::get('/', function () {
    return view('home');
})->name('home');

// Weather prediction routes
Route::get('/prediction', [WeatherController::class, 'index'])->name('prediction');
Route::post('/prediction', [WeatherController::class, 'search'])->name('prediction.search');
Route::post('/prediction/current-location', [WeatherController::class, 'currentLocation'])->name('prediction.current-location');

// Market prices route
Route::get('/market-prices', [MarketPriceController::class, 'index'])->name('market-prices');

// Crop suggestion route
Route::get('/crop-suggestion', [CropSuggestionController::class, 'index'])->name('crop-suggestion');
Route::post('/crop-suggestion/get', [CropSuggestionController::class, 'getSuggestions'])->name('crop-suggestion.get');

// Pest alerts route
Route::get('/pest-alerts', [PestAlertController::class, 'index'])->name('pest-alerts');
Route::post('/pest-alerts/get', [PestAlertController::class, 'getPestAlerts'])->name('pest-alerts.get');

// Weather warnings route
Route::get('/weather-warnings', [WeatherWarningController::class, 'index'])->name('weather-warnings');
Route::post('/weather-warnings/get', [WeatherWarningController::class, 'getWarnings'])->name('weather-warnings.get');

// Farming precautions route
Route::get('/farming-precautions', function () {
    return view('farming-precautions');
})->name('farming-precautions');

Route::post('/farming-precautions/get', [FarmingPrecautionsController::class, 'getPrecautions'])->name('farming-precautions.get');
