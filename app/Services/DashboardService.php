<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\ProductMovement;
use App\Models\Patientrecords;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Helper to calculate the date range based on filter input.
     */
    public function calculateDateRange($timespan, $start, $end)
    {
        $dateRange = new \stdClass();
        $dateRange->end = Carbon::now()->endOfDay();

        if ($timespan == 'custom' && $start && $end) {
            $dateRange->start = Carbon::parse($start)->startOfDay();
            $dateRange->end = Carbon::parse($end)->endOfDay();
        } elseif ($timespan == '7d') {
            $dateRange->start = Carbon::now()->subDays(6)->startOfDay();
        } elseif ($timespan == '90d') {
            $dateRange->start = Carbon::now()->subDays(89)->startOfDay();
        } elseif ($timespan == '1y') {
            $dateRange->start = Carbon::now()->subYear()->addDay()->startOfDay();
        } elseif ($timespan == 'all') {
            $minDate = ProductMovement::min('created_at');
            if ($minDate) {
                $dateRange->start = Carbon::parse($minDate)->startOfDay();
            } else {
                $dateRange->start = Carbon::now()->startOfDay();
            }
        } else { // Default to 30d
            $dateRange->start = Carbon::now()->subDays(29)->startOfDay();
        }

        // Ensure start is never after end
        if ($dateRange->start->gt($dateRange->end)) {
            $dateRange->start = $dateRange->end->copy()->startOfDay();
        }

        return $dateRange;
    }

    /**
     * Get human-readable label for timespan filter.
     */
    public function getTimespanLabel($timespan, $dateRange)
    {
        switch($timespan) {
            case '7d': return 'Last 7 Days';
            case '30d': return 'Last 30 Days';
            case '90d': return 'Last 90 Days';
            case '1y': return 'Last 1 Year';
            case 'all': return 'All Time';
            case 'custom': return $dateRange->start->format('M d, Y') . ' - ' . $dateRange->end->format('M d, Y');
            default: return 'Last 30 Days';
        }
    }

    /**
     * Helper to get consumption trend data (OUT only) for the line chart.
     * Fixed: Added join with 'barangays' for barangay filtering in patient record ID retrieval.
     */
    public function getConsumptionTrend($dateRange, $product_id, $barangay, $grouping) // Added $barangay
    {
        // Start query on ProductMovement
        $query = ProductMovement::where('product_movements.type', 'OUT')
            ->whereBetween('product_movements.created_at', [$dateRange->start, $dateRange->end])
            // --- FIX: Use traditional function syntax ---
            ->when($product_id, function ($query) use ($product_id) {
                return $query->where('product_movements.product_id', $product_id);
            });

        // Filter by Barangay if provided
        if ($barangay) {
            // Get patient record IDs for the specified barangay within the date range
            // Fixed: Added join with 'barangays' to filter by 'barangay_name'
            $patientRecordIds = Patientrecords::join('barangays', 'patientrecords.barangay_id', '=', 'barangays.id')
                                      ->where('barangays.barangay_name', $barangay)
                                      ->whereBetween('date_dispensed', [$dateRange->start, $dateRange->end])
                                      ->pluck('patientrecords.id');

            // Filter movements associated with these patient records based on description
            // This relies on the description format "Record: #[ID])"
            $query->where(function($q) use ($patientRecordIds) {
                if ($patientRecordIds->isEmpty()) {
                    $q->whereRaw('1 = 0'); // No matching patients, so no matching movements
                } else {
                    // Build OR conditions for each matching patient record ID
                    foreach ($patientRecordIds as $id) {
                        // Use parameter binding for safety? No, LIKE needs the value directly
                        $q->orWhere('description', 'LIKE', "%Record: #{$id})%");
                    }
                }
            });
        }

        // Period and Grouping Logic (remains the same)
        $periodStartDate = $dateRange->start->copy();
        if ($grouping == 'week') $periodStartDate->startOfWeek(Carbon::MONDAY);
        if ($grouping == 'month') $periodStartDate->startOfMonth();
        if ($periodStartDate->gt($dateRange->end)) {
            $periodStartDate = $dateRange->end->copy();
            if ($grouping == 'week') $periodStartDate->startOfWeek(Carbon::MONDAY);
            if ($grouping == 'month') $periodStartDate->startOfMonth();
        }

        $period = null;
        if ($grouping == 'week') {
            $period = CarbonPeriod::create($periodStartDate, '1 week', $dateRange->end->copy()->endOfWeek(Carbon::SUNDAY));
        } elseif ($grouping == 'month') {
            $period = CarbonPeriod::create($periodStartDate, '1 month', $dateRange->end->copy()->startOfMonth());
        } else {
            $period = CarbonPeriod::create($periodStartDate, '1 day', $dateRange->end);
        }

        $dbFormat = 'Y-m-d';
        $labelFormat = 'M d';
        $orderByColumn = 'date_group';
        $groupByColumn = 'date_group';
        switch ($grouping) {
            case 'week':
                $dbFormat = 'o-W';
                $labelFormat = '\WW Y (M d)';
                $selectRaw = "DATE_FORMAT(product_movements.created_at, '%x-%v') as date_group";
                break;
            case 'month':
                $dbFormat = 'Y-m';
                $labelFormat = 'M Y';
                $selectRaw = "DATE_FORMAT(product_movements.created_at, '%Y-%m') as date_group";
                break;
            default:
                $selectRaw = "DATE(product_movements.created_at) as date_group";
                break;
        }

        $dispensationTrend = $query
            ->select(DB::raw($selectRaw), DB::raw('SUM(product_movements.quantity) as total_quantity'))
            ->groupBy($groupByColumn)
            ->orderBy($orderByColumn, 'asc')
            ->get()
            ->pluck('total_quantity', $orderByColumn);

        // Fill in missing (remains the same)
        $labels = [];
        $data = [];
        if ($period) {
            foreach ($period as $date) {
                $key = $date->format($dbFormat);
                $label = $date->format($labelFormat);
                $labels[] = $label;
                $data[] = $dispensationTrend[$key] ?? 0;
            }
        }
        return [$labels, $data];
    }

    /**
     * --- NEW HELPER ---
     * Helper to get Patient Visit trend data for the new line chart.
     * Fixed: Added join with 'barangays' for barangay filtering.
     */
    public function getPatientVisitTrend($dateRange, $barangay, $drilldownProduct, $grouping)
    {
        // 1. Define Period and Grouping (copied from getConsumptionTrend)
        $periodStartDate = $dateRange->start->copy();
        if ($grouping == 'week') $periodStartDate->startOfWeek(Carbon::MONDAY);
        if ($grouping == 'month') $periodStartDate->startOfMonth();
        if ($periodStartDate->gt($dateRange->end)) {
            $periodStartDate = $dateRange->end->copy();
            if ($grouping == 'week') $periodStartDate->startOfWeek(Carbon::MONDAY);
            if ($grouping == 'month') $periodStartDate->startOfMonth();
        }

        $period = null;
        if ($grouping == 'week') {
            $period = CarbonPeriod::create($periodStartDate, '1 week', $dateRange->end->copy()->endOfWeek(Carbon::SUNDAY));
        } elseif ($grouping == 'month') {
            $period = CarbonPeriod::create($periodStartDate, '1 month', $dateRange->end->copy()->startOfMonth());
        } else {
            $period = CarbonPeriod::create($periodStartDate, '1 day', $dateRange->end);
        }

        $dbFormat = 'Y-m-d';
        $labelFormat = 'M d';
        $orderByColumn = 'date_group';
        $groupByColumn = 'date_group';
        switch ($grouping) {
            case 'week':
                $dbFormat = 'o-W';
                $labelFormat = '\WW Y (M d)';
                $selectRaw = "DATE_FORMAT(date_dispensed, '%x-%v') as date_group";
                break;
            case 'month':
                $dbFormat = 'Y-m';
                $labelFormat = 'M Y';
                $selectRaw = "DATE_FORMAT(date_dispensed, '%Y-%m') as date_group";
                break;
            default:
                $selectRaw = "DATE(date_dispensed) as date_group";
                break;
        }

        // 2. Get Patient Visits
        $patientVisitsQuery = Patientrecords::whereBetween('date_dispensed', [$dateRange->start, $dateRange->end])
            ->when($barangay, function ($q) use ($barangay) {
                // Fixed: Added join with 'barangays' to filter by 'barangay_name'
                $q->join('barangays', 'patientrecords.barangay_id', '=', 'barangays.id')
                  ->where('barangays.barangay_name', $barangay);
            })
            ->when($drilldownProduct, function ($query) use ($drilldownProduct) {
                return $query->whereHas('dispensedMedications', function ($q) use ($drilldownProduct) {
                    $q->where('generic_name', $drilldownProduct->generic_name)
                      ->where('brand_name', $drilldownProduct->brand_name)
                      ->where('strength', $drilldownProduct->strength)
                      ->where('form', $drilldownProduct->form);
                });
            })
            ->select(DB::raw($selectRaw), DB::raw('COUNT(DISTINCT patientrecords.id) as total_patients'))
            ->groupBy($groupByColumn)
            ->orderBy($orderByColumn, 'asc');

        // If barangay filter is applied, ensure join is present for the whole query
        if ($barangay) {
            $patientVisitsQuery->select('patientrecords.*'); // Ensure patientrecords fields are available
        }

        $patientVisits = $patientVisitsQuery->get()
            ->pluck('total_patients', $orderByColumn);

        // 3. Combine data using the generated period
        $labels = [];
        $data = [];
        if ($period) {
            foreach ($period as $date) {
                $key = $date->format($dbFormat);
                $label = $date->format($labelFormat);
                $labels[] = $label;
                $data[] = $patientVisits[$key] ?? 0;
            }
        }
        return [$labels, $data];
    }

    /**
     * Gets all-time seasonal trend data, can optionally align to existing labels.
     */
    public function getProductTrend($product_id, $alignLabels = null)
    {
        // Ensure start date is at least 3 years ago or the first movement, whichever is later
        $threeYearsAgo = Carbon::now()->subYears(3)->startOfMonth();
        $firstMovementDate = ProductMovement::where('product_id', $product_id)
                                            ->where('type', 'OUT')
                                            ->min('created_at');

        $startDate = $threeYearsAgo;
        if ($firstMovementDate) {
            $firstMovementMonthStart = Carbon::parse($firstMovementDate)->startOfMonth();
            if ($firstMovementMonthStart->gt($startDate)) {
                $startDate = $firstMovementMonthStart;
            }
        }
        // Ensure start date is not in the future
        if ($startDate->gt(Carbon::now())) {
            $startDate = Carbon::now()->startOfMonth();
        }

        $query = ProductMovement::where('type', 'OUT')
            ->where('product_id', $product_id)
            ->where('created_at', '>=', $startDate) // Use calculated start date
            ->groupBy('date_group') // Use alias for grouping
            ->orderBy('date_group', 'asc') // Use alias for ordering
            ->select( // Use select() here
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as date_group"),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->get() // Fetch selected columns
            ->pluck('total_quantity', 'date_group'); // Pluck using alias

        if ($query->isEmpty() && !$alignLabels) { // If no data AND not aligning, return empty
            return [[], []];
        }

        $labels = [];
        $data = [];
        $endDate = Carbon::now()->startOfMonth(); // Ensure period ends at the beginning of the current month

        // Determine the period based on alignment or query results
        if ($alignLabels) {
            $period = collect($alignLabels)->map(function($l) {
                // Try parsing different formats just in case
                try {
                    return Carbon::parse($l)->startOfMonth();
                } catch (\Exception $e) {
                    return null;
                }
            })->filter()->unique(); // Filter out nulls and ensure uniqueness

            // Fallback if alignment labels are invalid or empty
            if ($period->isEmpty()) {
                if ($query->isEmpty()) return [[],[]]; // No data, no alignment possible
                $periodStartDate = Carbon::parse($query->keys()->first() . '-01');
                if ($periodStartDate->gt($endDate)) $periodStartDate = $endDate->copy(); // Prevent start > end
                $period = CarbonPeriod::create($periodStartDate, '1 month', $endDate);
                $alignLabels = null; // Disable alignment
            } else {
                // Use the min/max of parsed labels for the period if aligning
                $period = CarbonPeriod::create($period->min(), '1 month', $period->max());
            }

        } else { // Not aligning
            if ($query->isEmpty()) return [[],[]]; // No data, return empty
            $periodStartDate = Carbon::parse($query->keys()->first() . '-01');
            // Ensure start date respects the 3-year limit
            if ($periodStartDate->lt($threeYearsAgo)) {
                $periodStartDate = $threeYearsAgo;
            }
            // Ensure start date is not after end date
            if ($periodStartDate->gt($endDate)) {
                $periodStartDate = $endDate->copy();
            }
            $period = CarbonPeriod::create($periodStartDate, '1 month', $endDate);
        }

        // Populate labels and data
        if ($period) { // Add null check for period
            foreach ($period as $date) {
                $key = $date->format('Y-m');
                if (!$alignLabels) { // Only generate new labels if not aligning
                    $labels[] = $date->format('M Y');
                }
                $data[] = $query[$key] ?? 0;
            }
        }

        // If aligning, labels are just the input alignLabels (already prepared)
        if ($alignLabels && $period) { // Add null check for period
            // Re-generate labels from the potentially adjusted period for consistency
            $labels = [];
            foreach($period as $date) {
                $labels[] = $date->format('M Y');
            }
        } elseif ($alignLabels) { // If aligning but period failed, return original labels
            $labels = $alignLabels;
        }

        return [$labels, $data];
    }

    /**
     * Calculates the "Days of Stock Remaining".
     */
    public function calculateStockForecast($daysOfHistory = 90)
    {
        if ($daysOfHistory <= 0) $daysOfHistory = 90; // Ensure positive days

        // 1. Get total dispensation (OUT)
        $consumption = ProductMovement::where('type', 'OUT')
            ->where('created_at', '>=', Carbon::now()->subDays($daysOfHistory))
            ->groupBy('product_id')
            ->select('product_id', DB::raw("SUM(quantity) as total_consumed"))
            ->pluck('total_consumed', 'product_id');

        // 2. Get the current stock level
        $currentStock = Inventory::where('is_archived', 2)
            ->groupBy('product_id')
            ->select('product_id', DB::raw("SUM(quantity) as current_quantity"))
            ->pluck('current_quantity', 'product_id');

        // 3. Get product details
        $products = Product::whereIn('id', $currentStock->keys())->get()->keyBy('id');

        $forecast = [];

        // 4. Calculate forecast
        foreach ($currentStock as $product_id => $stock) {

            if (!isset($products[$product_id])) continue;

            $totalConsumed = $consumption[$product_id] ?? 0;
            $avgDailyUsage = ($daysOfHistory > 0) ? $totalConsumed / $daysOfHistory : 0; // Avoid division by zero

            if ($avgDailyUsage > 0) {
                // Use max(0.01, ...) to avoid division by zero errors with tiny usage rates
                $daysRemaining = floor($stock / max(0.01, $avgDailyUsage));
            } else {
                $daysRemaining = INF;
            }

            $forecast[] = [
                'product_name' => $products[$product_id]->generic_name,
                'brand_name' => $products[$product_id]->brand_name,
                'current_stock' => $stock,
                'avg_daily_usage' => round($avgDailyUsage, 2),
                'days_remaining' => $daysRemaining,
            ];
        }

        // Sort by most urgent
        usort($forecast, function ($a, $b) {
            // Treat INF as very large number for sorting
            $aDays = ($a['days_remaining'] === INF) ? PHP_INT_MAX : $a['days_remaining'];
            $bDays = ($b['days_remaining'] === INF) ? PHP_INT_MAX : $b['days_remaining'];
            return $aDays <=> $bDays;
        });

        return $forecast;
    }

    /**
     * --- NEW HELPER ---
     * Get seasonal data formatted for AJAX response.
     */
        public function getSeasonalDataForAjax($seasonal_product_id, $compare_product_id)
        {
            $selectedSeasonalProduct = null;
            $compareSeasonalProduct = null;
            $seasonalLabels = [];
            $seasonalData = [];
            $compareData = [];
    
            if ($seasonal_product_id) {
                $selectedSeasonalProduct = Product::find($seasonal_product_id);
                if ($selectedSeasonalProduct) {
                    [$seasonalLabels, $seasonalData] = $this->getProductTrend($seasonal_product_id);
                }
            }
            if ($compare_product_id) {
                $compareSeasonalProduct = Product::find($compare_product_id);
                if ($compareSeasonalProduct) {
                    [$seasonalLabels, $compareData] = $this->getProductTrend($compare_product_id, $seasonalLabels);
                }
            }
    
            return [
                'labels'       => $seasonalLabels,
                'data'         => $seasonalData,
                'productName'  => $selectedSeasonalProduct->generic_name ?? null,
                'compareData'  => $compareData,
                'compareName'  => $compareSeasonalProduct->generic_name ?? null,
            ];
        }
    
        public function getFullDashboardData(array $inputs)
        {
            $timespan = $inputs['filter_timespan'] ?? '30d';
            $filter_barangay = $inputs['filter_barangay'] ?? null;
            $forecast_days = $inputs['forecast_days'] ?? 90;
            $grouping = $inputs['grouping'] ?? 'day';
            $drilldown_product_id = $inputs['drilldown_product_id'] ?? null;
            $drilldownProduct = $drilldown_product_id ? Product::find($drilldown_product_id) : null;
            $seasonal_product_id = $inputs['seasonal_product_id'] ?? Product::where('is_archived', 2)->value('id');
            $compare_product_id = $inputs['compare_product_id'] ?? null;
    
            $dateRange = $this->calculateDateRange(
                $timespan,
                $inputs['filter_start'] ?? null,
                $inputs['filter_end'] ?? null
            );
    
            // ... (rest of the data retrieval logic from the old controller)
    
            return [/* ... data ... */];
        }
    }
    