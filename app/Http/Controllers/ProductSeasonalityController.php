<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductSeasonalityController extends Controller
{
    /**
     * Analyzes sales from the previous day to reactively set season_peak
     * based on high-volume sales thresholds.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeRecentSales()
    {
        try {
            // ======================================================================
            // === ADJUSTABLE PARAMETERS ===
            // ======================================================================
            $allYearThreshold = 1000;
            $seasonalThreshold = 500;
            // ======================================================================

            $analysisDate = Carbon::yesterday();
            $seasonOfAnalysis = $this->getSeasonForDate($analysisDate);

            // 📊 Kunin lahat ng produkto na umabot sa minimum threshold kahapon.
            $productsWithSpike = DB::table('immutable_histories')
                ->where('status', 'delivered')
                ->whereDate('date_ordered', $analysisDate)
                ->select('generic_name', 'brand_name', 'form', 'strength', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('generic_name', 'brand_name', 'form', 'strength')
                ->having('total_quantity', '>=', $seasonalThreshold)
                ->get();

            if ($productsWithSpike->isEmpty()) {
                return response()->json(['status' => 'info', 'message' => 'No products met the high-volume sales threshold yesterday.']);
            }

            $trueUpdateCount = 0;
            
            foreach ($productsWithSpike as $productData) {
                $predictedSeason = null;
                $quantitySold = $productData->total_quantity;

                // 🧠 I-apply ang rules
                if ($quantitySold >= $allYearThreshold) {
                    $predictedSeason = 'All-Year';
                } elseif ($quantitySold >= $seasonalThreshold) {
                    $predictedSeason = $seasonOfAnalysis;
                }

                // Kung may na-determine na season, ituloy ang pag-update
                if ($predictedSeason) {
                    $product = Product::where('generic_name', $productData->generic_name)
                        ->where('brand_name', $productData->brand_name)
                        ->where('form', $productData->form)
                        ->where('strength', $productData->strength)
                        ->first();
                    
                    // I-update lang kung may pagbabago
                    if ($product && $product->season_peak !== $predictedSeason) {
                        $product->season_peak = $predictedSeason;
                        $product->save();
                        $trueUpdateCount++;
                        Log::info("Product '{$product->generic_name}' peak updated to '{$predictedSeason}' based on yesterday's sales of {$quantitySold} units.");
                    }
                }
            }

            $message = $trueUpdateCount > 0 
                ? "Reactive analysis complete. {$trueUpdateCount} products had new classifications based on yesterday's sales."
                : "Reactive analysis complete. High-volume products were found, but their classifications remain unchanged.";

            return response()->json([
                'status' => 'success', 
                'message' => $message, 
                'products_with_new_classification' => $trueUpdateCount
            ]);

        } catch (Exception $e) {
            Log::error('Reactive seasonality analysis failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred during the reactive analysis.'], 500);
        }
    }
    
    /**
     * Helper function to determine the season for a given date.
     * @param \Carbon\Carbon $date
     * @return string 'Tag-init' or 'Tag-ulan'
     */
    private function getSeasonForDate($date)
    {
        $month = $date->month;
        // Dry Season (Tag-init) is defined as December to May
        if (in_array($month, [12, 1, 2, 3, 4, 5])) {
            return 'Tag-init';
        }
        // Wet Season (Tag-ulan) is defined as June to November
        return 'Tag-ulan';
    }
}