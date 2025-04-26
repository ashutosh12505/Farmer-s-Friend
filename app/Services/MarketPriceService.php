<?php

namespace App\Services;

class MarketPriceService
{
    public function getMarketPrices()
    {
        $csvFile = public_path('crop_price.csv');
        
        if (!file_exists($csvFile)) {
            return [];
        }

        $prices = [];
        $handle = fopen($csvFile, 'r');
        
        // Get header row to extract year names
        $headers = fgetcsv($handle);
        $yearNames = array_slice($headers, 1); // Skip the 'Crop' column
        
        $serialNo = 1;
        while (($data = fgetcsv($handle)) !== false) {
            $priceEntry = [
                'serial_no' => $serialNo++,
                'crop_name' => $data[0] ?? ''
            ];
            
            // Add each year's price using the actual year names from the header
            foreach ($yearNames as $index => $year) {
                $priceEntry[$year] = $data[$index + 1] ?? '';
            }
            
            $prices[] = $priceEntry;
        }
        
        fclose($handle);
        
        return $prices;
    }
} 