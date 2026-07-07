<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function updateRates()
    {
        try {
            $response = Http::get('https://open.er-api.com/v6/latest/USD');
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (($data['result'] ?? '') !== 'success') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'API returned unsuccessful result.'
                    ], 500);
                }

                $rates = $data['rates'];
                
                $updatedCount = 0;
                foreach ($rates as $currencyCode => $rate) {
                    $updated = Country::where('currency', $currencyCode)
                        ->update(['currency_value' => $rate]);
                    
                    if ($updated) {
                        $updatedCount++;
                    }
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => "Successfully updated {$updatedCount} currency rates.",
                    'last_update' => $data['time_last_update_utc'] ?? null
                ]);
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch exchange rates from the API.'
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
