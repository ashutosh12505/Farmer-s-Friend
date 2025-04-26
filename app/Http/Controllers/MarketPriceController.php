<?php

namespace App\Http\Controllers;

use App\Services\MarketPriceService;
use Illuminate\Http\Request;

class MarketPriceController extends Controller
{
    protected $marketPriceService;

    public function __construct(MarketPriceService $marketPriceService)
    {
        $this->marketPriceService = $marketPriceService;
    }

    public function index()
    {
        $prices = $this->marketPriceService->getMarketPrices();
        return view('market-prices', compact('prices'));
    }
} 