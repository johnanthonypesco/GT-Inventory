<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIGeneratedExecutiveSummary;
use App\Models\Admin;
use App\Models\Conversation;
use App\Models\ImmutableHistory;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\Staff;
use App\Models\SuperAdmin;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    // The single API endpoint for all AI requests via OpenRouter.
    private const OPENROUTER_CHAT_API_URL = 'https://openrouter.ai/api/v1/chat/completions';

    /**
     * Handles all AI-related requests from the dashboard.
     */
    public function handleAiRequest(Request $request): JsonResponse
    {
        try {
            if ($request->input('request_type') === 'dashboard_analysis') {
                $aiModel = $request->input('ai_model', 'deepseek/deepseek-r1:free'); 
                $chartData = $request->input('chart_data', []);
                $forceRefresh = $request->input('force_refresh', false);
                // MODIFIED: Get the selected API key type from the request
                $apiKeyType = $request->input('api_key_type', 'primary'); 

                // MODIFIED: Pass the key type to the analysis function
                $result = $this->generateDashboardAnalysis($aiModel, $chartData, $forceRefresh, $apiKeyType);

                return response()->json($result);
            }
            return response()->json(['error' => 'Invalid AI request type specified.'], 400);
        } catch (\Exception $e) {
            Log::error("AI Request Handler failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * Generates a complete dashboard analysis, checking the database unless a force refresh is requested.
     */
    /**
     * Generates a complete dashboard analysis, checking the database unless a force refresh is requested.
     */
    private function generateDashboardAnalysis(string $aiModel, array $chartData, bool $forceRefresh = false, string $apiKeyType = 'primary'): array
    {
        $latestSummary = AIGeneratedExecutiveSummary::latest()->first();
        $response = [];

        if ($forceRefresh === false && $latestSummary && $latestSummary->created_at->gt(now()->subHour())) {
            $response = [
                'analysis' => $latestSummary->summary_data,
                'expires_at' => $latestSummary->created_at->addHour()->toIso8601String(),
            ];
        } else {
            $rawAiResponseForLogging = '';
             try {
                $dataForPrompt = $this->gatherDataForAnalysis();
                $prompt = $this->createUnifiedAnalysisPrompt($dataForPrompt, $chartData);
                
                // MODIFIED: Pass the apiKeyType to the dispatch function
                $aiResponse = $this->_dispatchAiRequest($prompt, $aiModel, $apiKeyType, true);
                $rawAiResponseForLogging = $aiResponse['content']; 
                $analysis = json_decode($rawAiResponseForLogging, true);

                if (!is_array($analysis) || !isset($analysis['anomalies'])) {
                    $cleanedJsonString = preg_replace('/```json\s*|\s*```/', '', $rawAiResponseForLogging);
                    $analysis = json_decode($cleanedJsonString, true);
                    if (!is_array($analysis) || !isset($analysis['anomalies'])) {
                        throw new \Exception('AI analysis response was malformed.');
                    }
                }

                $analysis['_model_used'] = $aiResponse['model_name'];
                
                AIGeneratedExecutiveSummary::create(['summary_data' => $analysis]);

                $response = [
                    'analysis' => $analysis,
                    'expires_at' => now()->addHour()->toIso8601String(),
                ];

            } catch (\Exception $e) {
                Log::error('Exception in generateDashboardAnalysis: ' . $e->getMessage(), [
                    'raw_ai_response' => $rawAiResponseForLogging
                ]);
                $response = [
                    'analysis' => [
                        'anomalies' => [['type' => 'negative', 'message' => 'Error generating analysis: ' . $e->getMessage() . ' Check logs.']],
                        'recommendations' => [['message' => 'Please check the system logs or try again later.']],
                        'chart_analysis' => 'Could not generate chart analysis due to a system error.'
                    ],
                    'expires_at' => now()->addMinutes(1)->toIso8601String(),
                ];
            }
        }

        $response['history'] = AIGeneratedExecutiveSummary::latest()->limit(10)->get();
        
        return $response;
    }


    /**
     * Gathers key business metrics from the database for AI analysis.
     */
     private function gatherDataForAnalysis(): array
    {
        $startDate = Carbon::now('Asia/Manila')->subDays(30)->startOfDay();
        $endDate = Carbon::now('Asia/Manila')->endOfDay();

        $query = ImmutableHistory::where('status', 'delivered')->whereBetween('date_ordered', [$startDate, $endDate]);

        $totalRevenue = $query->sum(DB::raw('quantity * price'));
        $totalOrders = $query->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $topSeller = (clone $query)
            ->select('generic_name', DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('generic_name')->orderByDesc('total_revenue')->first();

        $overallStats = [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'top_selling_product' => ['name' => $topSeller->generic_name ?? 'N/A', 'revenue' => $topSeller->total_revenue ?? 0]
        ];

        $lowStockProducts = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
            ->groupBy('products.generic_name')->having('total_quantity', '<=', 50)
            ->orderBy('total_quantity', 'asc')->limit(5)->get();

        return [
            'current_date' => Carbon::now('Asia/Manila')->format('Y-m-d'),
            'analysis_period' => 'Last 30 Days',
            'overall_stats' => $overallStats,
            'low_stock_products' => $lowStockProducts->toArray(),
        ];
    }

    /**
     * Creates a new, unified prompt for the AI to get all analysis components at once.
     */
    private function createUnifiedAnalysisPrompt(array $dbData, array $chartData): string
    {
        $db_json = json_encode($dbData, JSON_PRETTY_PRINT);
        $chart_json = json_encode($chartData, JSON_PRETTY_PRINT);
        $currentDate = now('Asia/Manila')->format('F j, Y');
        
        return <<<PROMPT
        **Role and Goal:** You are an expert business intelligence analyst for a leading pharmaceutical e-commerce and distribution company in the Philippines. Your primary objective is to analyze the provided data from the last 30 days and generate a concise, data-driven report for the company's executive team.

        **Contextual Information:**
        * **Company Location:** Tarlac City, Cabanatuan City, Philippines
        * **Current Time in the Philippines:** {$currentDate}
        * **Current Season in the Philippines:** It is currently August, which is the peak of the 'tag-ulan' (rainy season). This season typically runs from June to November. Common illnesses include flu, dengue fever, colds, and other respiratory infections.

        **Mandatory Output Format:**
        Your response **MUST** be a single, raw, and valid JSON object. Do not include any explanatory text, markdown formatting, or comments before or after the JSON structure.

        ```json
        {
          "anomalies": [
            {
              "type": "positive|negative|warning",
              "message": "A concise description of a key finding from the database stats."
            }
          ],
          "recommendations": [
            {
              "message": "A short, actionable step linking database and chart data."
            }
          ],
          "chart_analysis": "A holistic summary of all charts combined, focusing on the business story."
        }
        ```

        **Detailed Directives:**

        1.  **Anomaly Detection:** Scrutinize the `DATABASE STATISTICS`. Identify 1-3 of the most critical deviations or insights. Prioritize significant events, such as a best-selling drug being critically low on stock (`low_stock_products`), a sudden spike in demand for a specific product, or unusually low sales for a typically popular item. Relate findings to the current rainy season where applicable.

        2.  **Strategic Recommendations:** Generate 1-2 high-impact, actionable recommendations. You **must cross-reference `DATABASE STATISTICS` with `CHART DATA`** to form these insights. For example, if a product is listed in `low_stock_products` AND is a top performer in the `Products Delivered` chart, recommend an urgent restock and a review of safety stock levels.

        3.  **Synthesized Chart Analysis:** Analyze all `CHART DATA` as a single, coherent narrative. **Do not describe each chart individually.** Instead, synthesize them into one insightful paragraph (3-5 sentences) that reveals the overarching business story. Identify the most significant trend or relationship between the charts (e.g., "While overall revenue is up, the growth is driven by only two products, indicating a high-risk portfolio that needs diversification.").

        4.  **Language and Formatting:** Adhere strictly to professional business English. All currency must be in Philippine Pesos (PHP), formatted with commas (e.g., `₱1,234,567`).

        --- DATABASE STATISTICS (Last 30 Days) ---
        {$db_json}

        --- CHART DATA ---
        {$chart_json}
        PROMPT;
    }

    /**
     * MODIFIED: This function now exclusively uses OpenRouter for all AI requests.
     * All old logic for direct Gemini and Mistral calls has been removed.
     */
    private function _dispatchAiRequest(string $prompt, string $model, string $apiKeyType = 'primary', bool $jsonMode = false): array
    {
        // MODIFIED: Select the key from the config array
        $apiKey = config('services.openrouter.keys.' . $apiKeyType);

        if (!$apiKey) {
            // Fallback to primary key if the selected one is not found or null
            $apiKey = config('services.openrouter.keys.primary');
            if (!$apiKey) {
                throw new \Exception('OpenRouter API Key is not set for selected type (' . $apiKeyType . ') or primary. Please check your .env and config/services.php file.');
            }
        }

        $payload = [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.5,
            'max_tokens' => 4096,
        ];

        if ($jsonMode) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::timeout(120)
            ->withToken($apiKey)
            ->withHeaders([
                'HTTP-Referer' => config('services.openrouter.referer', config('app.url')),
                'X-Title' => config('app.name'),
            ])
            ->post(self::OPENROUTER_CHAT_API_URL, $payload);

        if (!$response->successful()) {
            Log::error('OpenRouter API request failed: ' . $response->body(), [
                'status' => $response->status(),
                'model' => $model
            ]);
            throw new \Exception('Failed to get a response from OpenRouter: ' . $response->body());
        }

        return [
            'content'    => $response->json()['choices'][0]['message']['content'] ?? '',
            'model_name' => $model,
        ];
    }

    /**
     * Display the main dashboard.
     */
    public function showDashboard()
    {
        $locations = Location::all();
        $currentUser = Auth::user();

        $adminsidebar_counter = 0;
        $unreadMessagesAdmin = 0;
        $unreadMessagesSuperAdmin = 0;
        $unreadMessagesStaff = 0;

        if ($currentUser instanceof SuperAdmin) {
            $unreadMessagesSuperAdmin = Conversation::where('is_read', false)->where('receiver_type', 'super_admin')->where('receiver_id', $currentUser->id)->count();
            $adminsidebar_counter = $unreadMessagesSuperAdmin;
        } elseif ($currentUser instanceof Admin) {
            $unreadMessagesAdmin = Conversation::where('is_read', false)->where('receiver_type', 'admin')->where('receiver_id', $currentUser->id)->count();
            $adminsidebar_counter = $unreadMessagesAdmin;
        } elseif ($currentUser instanceof Staff) {
            $unreadMessagesStaff = Conversation::where('is_read', false)->where('receiver_type', 'staff')->where('receiver_id', $currentUser->id)->count();
            $adminsidebar_counter = $unreadMessagesStaff;
        }

        $totalOrders = ImmutableHistory::where('status', 'delivered')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $totalRevenue = ImmutableHistory::where('status', 'delivered')
            ->select(DB::raw('SUM(quantity * price) as total_revenue'))
            ->first()->total_revenue ?? 0;

        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $topSeller = ImmutableHistory::where('status', 'delivered')
            ->select('generic_name', DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('generic_name')
            ->orderByDesc('total_revenue')
            ->first();

        $mostSoldProducts = ImmutableHistory::where('status', 'delivered')
            ->select('generic_name', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('generic_name')
            ->orderByDesc('total_quantity')
            ->limit(6)
            ->get();

        $lowSoldProductsQuery = ImmutableHistory::where('status', 'delivered')
            ->select('generic_name', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('generic_name')
            ->having('total_quantity', '<=', 10)
            ->orderBy('total_quantity')
            ->limit(6)
            ->get();

        $moderateSoldProducts = ImmutableHistory::where('status', 'delivered')
            ->select('generic_name', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('generic_name')
            ->having('total_quantity', '>', 10)
            ->having('total_quantity', '<=', 50)
            ->orderByDesc('total_quantity')
            ->limit(6)
            ->get();

        $lowStockProductsList = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
            ->groupBy('products.generic_name')
            ->having('total_quantity', '<=', 50)
            ->get();

        $labels = $mostSoldProducts->pluck('generic_name')->toArray();
        $data = $mostSoldProducts->pluck('total_quantity')->toArray();

        $lowSoldLabels = $lowSoldProductsQuery->pluck('generic_name')->toArray();
        $lowSoldData = $lowSoldProductsQuery->pluck('total_quantity')->toArray();

        $moderateSoldLabels = $moderateSoldProducts->pluck('generic_name')->toArray();
        $moderateSoldData = $moderateSoldProducts->pluck('total_quantity')->toArray();

        $availableYears = Order::select(DB::raw('YEAR(date_ordered) as year'))
            ->distinct()
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->merge(Inventory::select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year'))
            ->unique()
            ->sortDesc()
            ->values();

        $orderStatusCounts = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.dashboard', [
            'locations' => $locations,
            'currentUser' => $currentUser,
            'adminsidebar_counter' => $adminsidebar_counter,
            'unreadMessagesAdmin' => $unreadMessagesAdmin,
            'unreadMessagesSuperAdmin' => $unreadMessagesSuperAdmin,
            'unreadMessagesStaff' => $unreadMessagesStaff,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'cancelledOrders' => $cancelledOrders,
            'totalRevenue' => $totalRevenue,
            'averageOrderValue' => $averageOrderValue,
            'topSeller' => $topSeller,
            'lowStockProducts' => $lowStockProductsList,
            'labels' => $labels,
            'data' => $data,
            'lowSoldLabels' => $lowSoldLabels,
            'lowSoldData' => $lowSoldData,
            'moderateSoldLabels' => $moderateSoldLabels,
            'moderateSoldData' => $moderateSoldData,
            'availableYears' => $availableYears,
            'orderStatusCounts' => $orderStatusCounts,
        ]);
    }

    /**
     * Provide inventory data for charts.
     */
    public function getInventoryLevels($locationId = null)
    {
        $query = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'));

        if ($locationId) {
            $query->where('inventories.location_id', $locationId);
        }

        $inventoryData = $query->groupBy('products.generic_name')
            ->orderBy('total_quantity', 'ASC')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $inventoryData->pluck('generic_name'),
            'inventoryData' => $inventoryData->pluck('total_quantity')
        ]);
    }

    /**
     * Provide product delivery data for charts, filtered by province.
     */
    public function getFilteredDeductedQuantities($year, $month, $province = null)
    {
        $deductedQuery = ImmutableHistory::where('status', 'delivered')
            ->whereYear('date_ordered', $year)
            ->whereMonth('date_ordered', $month);

        if ($province) {
            $deductedQuery->where('province', $province);
        }

        $deductedQuantities = $deductedQuery->select(
                'generic_name',
                DB::raw('SUM(quantity) as total_deducted')
            )
            ->groupBy('generic_name')
            ->orderBy('total_deducted', 'DESC')
            ->limit(10)
            ->get();

        return response()->json([
            'labels'        => $deductedQuantities->pluck('generic_name'),
            'deductedData'  => $deductedQuantities->pluck('total_deducted'),
        ]);
    }

    /**
     * Provide revenue data for charts based on different time periods.
     */
    public function getRevenueData($period, $year, $month = null, $week = null)
    {
        if (!in_array($period, ['day', 'week', 'month', 'year'])) {
            return response()->json(['error' => 'Invalid period'], 400);
        }

        $query = ImmutableHistory::where('status', 'delivered')
            ->whereYear('date_ordered', $year);

        switch ($period) {
            case 'day':
                if (!$month) return response()->json(['error' => 'Month required for daily data'], 400);
                $query->whereMonth('date_ordered', $month)
                    ->select(DB::raw('DAY(date_ordered) as period_value'), DB::raw('SUM(quantity * price) as total_revenue'))
                    ->groupBy('period_value')->orderBy('period_value');
                break;
            case 'week':
                if (!$month) return response()->json(['error' => 'Month required for weekly data'], 400);
                $query->whereMonth('date_ordered', $month)
                    ->select(DB::raw('WEEK(date_ordered, 1) - WEEK(DATE_FORMAT(date_ordered, "%Y-%m-01"), 1) + 1 as period_value'), DB::raw('SUM(quantity * price) as total_revenue'))
                    ->groupBy('period_value')->orderBy('period_value');
                break;
            case 'month':
                $query->select(DB::raw('MONTH(date_ordered) as period_value'), DB::raw('SUM(quantity * price) as total_revenue'))
                    ->groupBy('period_value')->orderBy('period_value');
                break;
            case 'year':
                $query->select(DB::raw('YEAR(date_ordered) as period_value'), DB::raw('SUM(quantity * price) as total_revenue'))
                    ->groupBy('period_value')->orderBy('period_value');
                break;
        }

        $data = $query->get()->keyBy('period_value');
        $labels = [];
        $values = [];
        $valueMap = $data->pluck('total_revenue', 'period_value')->toArray();

        switch ($period) {
            case 'day':
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $labels[] = date('M j', mktime(0, 0, 0, $month, $d, $year));
                    $values[] = $valueMap[$d] ?? 0;
                }
                break;
            case 'week':
                $firstOfMonth = new \DateTime("$year-$month-01");
                $daysInMonth = (int)$firstOfMonth->format('t');
                $startWeekday = (int)$firstOfMonth->format('N'); // 1 (Mon) to 7 (Sun)
                $weeksInMonth = (int)ceil(($daysInMonth + ($startWeekday - 1)) / 7);
                for ($w = 1; $w <= $weeksInMonth; $w++) {
                    $labels[] = "Week $w";
                    $values[] = $valueMap[$w] ?? 0;
                }
                break;
            case 'month':
                $monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = $monthNames[$m - 1];
                    $values[] = $valueMap[$m] ?? 0;
                }
                break;
            case 'year':
                $startYear = ImmutableHistory::min(DB::raw('YEAR(date_ordered)')) ?? $year;
                for ($y = $startYear; $y <= $year; $y++) {
                    $labels[] = (string)$y;
                    $values[] = $valueMap[$y] ?? 0;
                }
                break;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    /**
     * Provide trending and predicted product data.
     */
    public function getTrendingProducts(Request $request)
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $seasonFilter = $request->input('season', 'all');
        $today = Carbon::now('Asia/Manila');
        $startOfHistory = $today->copy()->subYears(2)->startOfYear(); 

        $historicalData = ImmutableHistory::where('status', 'delivered')
            ->where('date_ordered', '>=', $startOfHistory)
            ->join('products', 'immutable_histories.generic_name', '=', 'products.generic_name')
            ->select(
                'products.id as product_id',
                'products.generic_name',
                'products.season_peak',
                DB::raw('YEAR(immutable_histories.date_ordered) as year'),
                DB::raw('MONTH(immutable_histories.date_ordered) as month'),
                DB::raw('SUM(immutable_histories.quantity) as total_quantity')
            )
            ->groupBy('product_id', 'generic_name', 'season_peak', 'year', 'month')
            ->get()
            ->groupBy('product_id');

        $allProducts = Product::all();
        $predictions = [];

        foreach ($allProducts as $product) {
            $productData = $historicalData->get($product->id);
            if (!$productData) continue;

            $monthlyAverages = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthlyAverages[$m] = $productData->where('month', $m)->avg('total_quantity') ?? 0;
            }
            $historicalOverallAverage = collect($monthlyAverages)->avg();

            $currentSales = $productData->where('year', $today->year)->where('month', $today->month)->sum('total_quantity') ?? 0;
            $m2_sales = $productData->where('year', $today->copy()->subMonth()->year)->where('month', $today->copy()->subMonth()->month)->sum('total_quantity') ?? 0;
            $m3_sales = $productData->where('year', $today->copy()->subMonths(2)->year)->where('month', $today->copy()->subMonths(2)->month)->sum('total_quantity') ?? 0;
            $recentTrend = ($currentSales * 0.5) + ($m2_sales * 0.3) + ($m3_sales * 0.2);

            $nextMonth = $today->copy()->addMonth();
            $nextMonthHistoricalAvg = $monthlyAverages[$nextMonth->month] ?? 0;
            $basePrediction = ($nextMonthHistoricalAvg * 0.6) + ($recentTrend * 0.4);

            $nextMonthSeason = match ($nextMonth->month) {
                3, 4, 5 => 'tag-init',
                6, 7, 8, 9, 10, 11 => 'tag-ulan',
                default => 'all-year',
            };

            $seasonalMultiplier = 1.0;
            if ($product->season_peak !== 'all-year') {
                if ($product->season_peak === $nextMonthSeason) $seasonalMultiplier = 1.25;
                else $seasonalMultiplier = 0.80;
            }
            $finalPrediction = $basePrediction * $seasonalMultiplier;
            
            $percentageChange = 0;
            if ($currentSales > 0) {
                $percentageChange = (($finalPrediction - $currentSales) / $currentSales) * 100;
            } elseif ($finalPrediction > 0) {
                $percentageChange = 100;
            }

            $statusText = '';
            if ($percentageChange >= 25) {
                $statusText = 'Strong potential for a significant sales increase.';
            } elseif ($percentageChange > 5) {
                $statusText = 'A slight increase in sales is expected.';
            } elseif ($percentageChange < -20) {
                $statusText = 'High possibility of a significant drop in sales.';
            } elseif ($percentageChange < -5) {
                $statusText = 'Sales may decrease in the coming month.';
            } else {
                $statusText = 'No significant change is expected in the sales forecast.';
            }

            $predictions[] = [
                'id' => $product->id,
                'generic_name' => $product->generic_name,
                'form' => $product->form,
                'strength' => $product->strength,
                'brand_name' => $product->brand_name,
                'season_peak' => $product->season_peak,
                'current_sales' => round($currentSales),
                'next_month_prediction' => round($finalPrediction),
                'historical_avg' => round($historicalOverallAverage, 2),
                'prediction_percentage_change' => round($percentageChange),
                'prediction_status_text' => $statusText,
            ];
        }

        $predictionsCollection = collect($predictions);
        if ($seasonFilter !== 'all') {
            $predictionsCollection = $predictionsCollection->filter(fn($p) => $p['season_peak'] === $seasonFilter);
        }

        $trendingProducts = $predictionsCollection->sortByDesc('current_sales')->take(10)->values();
        $predictedPeaks = $predictionsCollection->sortByDesc('next_month_prediction')->take(6)->values();

        return response()->json([
            'trending_products' => $trendingProducts,
            'predicted_peaks' => $predictedPeaks
        ]);
    }
    
    /**
     * Get filtered total revenue for the sales card modal.
     */
    public function getFilteredTotalRevenue(Request $request)
    {
        $filters = $request->validate([
            'period' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = ImmutableHistory::where('status', 'delivered');

        switch ($filters['period']) {
            case 'today':
                $query->whereDate('date_ordered', Carbon::today('Asia/Manila'));
                break;
            case 'this_week':
                $query->whereBetween('date_ordered', [Carbon::now('Asia/Manila')->startOfWeek(), Carbon::now('Asia/Manila')->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('date_ordered', Carbon::now('Asia/Manila')->month)->whereYear('date_ordered', Carbon::now('Asia/Manila')->year);
                break;
            case 'this_year':
                $query->whereYear('date_ordered', Carbon::now('Asia/Manila')->year);
                break;
            case 'custom':
                if ($filters['start_date'] && $filters['end_date']) {
                    $query->whereBetween('date_ordered', [Carbon::parse($filters['start_date'])->startOfDay(), Carbon::parse($filters['end_date'])->endOfDay()]);
                }
                break;
            case '7d':
                $query->where('date_ordered', '>=', Carbon::now('Asia/Manila')->subDays(7)->startOfDay());
                break;
            case 'all_time':
                break;
        }

        $totalRevenue = $query->sum(DB::raw('quantity * price'));
        
        return response()->json(['total_revenue' => $totalRevenue]);
    }
    
}