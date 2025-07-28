<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIGeneratedExecutiveSummary;
use App\Models\Admin;
use App\Models\Conversation;
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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    // Constants for API URLs remain the same.
    private const MISTRAL_CHAT_API_URL = 'https://api.mistral.ai/v1/chat/completions';

    private function getCacheKeyForSummary(array $filters): string
    {
        $period = $filters['period'] ?? '7d';
        $startDate = $filters['start_date'] ?? '';
        $endDate = $filters['end_date'] ?? '';
        return 'ai_summary_' . $period . '_' . $startDate . '_' . $endDate;
    }
    public function handleAiRequest(Request $request): JsonResponse
    {
        $requestType = $request->input('request_type');
        $aiModel = $request->input('ai_model', 'gemini-1.5-flash');
        // Get the summary filters from the request, with a default value.
        $summaryFilters = $request->input('summary_filters', ['period' => '7d', 'start_date' => null, 'end_date' => null]);

        try {
            switch ($requestType) {
                case 'executive_summary_only':
                    $summaryData = $this->generateAndCacheExecutiveSummary($aiModel, $summaryFilters);
                    return response()->json(['summary_data' => $summaryData]);

                case 'combined_analysis':
                    $chartData = $request->input('chart_data', []);
                    $summaryResult = $this->generateAndCacheExecutiveSummary($aiModel, $summaryFilters);
                    $analysisResult = $this->analyzeChartsWithAI($chartData, $aiModel);
                    return response()->json([
                        'summary_data' => $summaryResult,
                        'chart_analysis_data' => $analysisResult,
                    ]);

                default:
                    return response()->json(['error' => 'Invalid AI request type specified.'], 400);
            }
        } catch (\Exception $e) {
            Log::error("AI Request Handler failed for type '{$requestType}': " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'An unexpected error occurred while processing the AI request.'], 500);
        }
    }

    public function showDashboard()
    {
        $locations = Location::all();
        $currentUser = Auth::user();

        // ... (Walang pagbabago sa bahaging ito para sa unread messages)
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
        
        // Direct database queries without caching
        $totalOrders = Order::where('status', 'delivered')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        $totalRevenue = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->where('orders.status', 'delivered')->sum(DB::raw('orders.quantity * exclusive_deals.price'));

        $mostSoldProducts = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
            ->where('orders.status', 'delivered')
            ->groupBy('products.generic_name')->orderBy('total_quantity', 'DESC')->limit(6)->get();

        $lowSoldProductsQuery = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
            ->where('orders.status', 'delivered')->groupBy('products.generic_name')->having('total_quantity', '<=', 10)
            ->orderBy('total_quantity', 'ASC')->limit(6)->get();

        $moderateSoldProducts = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
            ->where('orders.status', 'delivered')->groupBy('products.generic_name')->having('total_quantity', '>', 10)
            ->having('total_quantity', '<=', 50)->orderBy('total_quantity', 'DESC')->limit(6)->get();
        
        $lowStockProductsList = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
            ->groupBy('products.generic_name')->having('total_quantity', '<=', 50)->get();
        
        $labels = $mostSoldProducts->pluck('generic_name')->toArray();
        $data = $mostSoldProducts->pluck('total_quantity')->toArray();

        $lowSoldLabels = $lowSoldProductsQuery->pluck('generic_name')->toArray();
        $lowSoldData = $lowSoldProductsQuery->pluck('total_quantity')->toArray();

        $moderateSoldLabels = $moderateSoldProducts->pluck('generic_name')->toArray();
        $moderateSoldData = $moderateSoldProducts->pluck('total_quantity')->toArray();

        $availableYears = Order::select(DB::raw('YEAR(date_ordered) as year'))->distinct()->orderBy('year', 'DESC')->pluck('year')
            ->merge(Inventory::select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year'))
            ->unique()->sortDesc()->values();

        $orderStatusCounts = Order::select('status', DB::raw('COUNT(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray();
        $executiveSummaryData = null;

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
            'lowStockProducts' => $lowStockProductsList,
            'labels' => $labels,
            'data' => $data,
            'lowSoldLabels' => $lowSoldLabels,
            'lowSoldData' => $lowSoldData,
            'moderateSoldLabels' => $moderateSoldLabels,
            'moderateSoldData' => $moderateSoldData,
            'availableYears' => $availableYears,
            'orderStatusCounts' => $orderStatusCounts,
            'executiveSummaryData' => $executiveSummaryData,
        ]);
    }

    private function generateAndCacheExecutiveSummary(string $aiModel, array $filters): array
    {
        $cacheKey = $this->getCacheKeyForSummary($filters);

        // Check if a valid summary already exists in the cache for these exact filters.
        if ($cachedData = Cache::get($cacheKey)) {
            if (now()->lessThan(Carbon::parse($cachedData['expires_at']))) {
                return $cachedData;
            }
        }

        try {
            $dataForPrompt = $this->gatherDataForSummary($filters);
            $prompt = $this->createExecutiveSummaryPrompt($dataForPrompt, $filters);
            $response = $this->_dispatchAiRequest($prompt, $aiModel, true);
            $summary = json_decode($response['content'], true);

            if (!is_array($summary) || !isset($summary['kpis'])) {
                Log::warning('AI executive summary response was not in the expected JSON format.', ['response' => $response['content']]);
                throw new \Exception('AI summary response was malformed.');
            }

            $summary['_model_used'] = $response['model_name'];
            AIGeneratedExecutiveSummary::create(['summary_data' => $summary]);

            $cacheData = ['summary' => $summary, 'expires_at' => now()->addHour()->toIso8601String()];
            Cache::put($cacheKey, $cacheData, now()->addHour());

            return $cacheData;
        } catch (\Exception $e) {
            Log::error('Exception in generateAndCacheExecutiveSummary: ' . $e->getMessage());
            return ['summary' => $this->getDefaultSummaryStructure($e->getMessage()), 'expires_at' => now()->addMinutes(2)->toIso8601String()];
        }
    }

    private function analyzeChartsWithAI(array $chartData, string $aiModel): array
    {
        try {
            if (empty($chartData)) {
                return ['analysis' => 'No chart data provided for analysis. Please ensure charts are visible on the dashboard.', 'model' => 'N/A'];
            }
            $prompt = $this->createChartAnalysisPrompt($chartData);
            $response = $this->_dispatchAiRequest($prompt, $aiModel, false);
            return ['analysis' => $response['content'], 'model' => $response['model_name']];
        } catch(\Exception $e) {
            Log::error('Exception in analyzeChartsWithAI: ' . $e->getMessage());
            return ['analysis' => 'Error: Could not generate chart analysis. ' . $e->getMessage(), 'model' => 'N/A'];
        }
    }

    private function _dispatchAiRequest(string $prompt, string $model, bool $jsonMode = false): array
    {
        if (str_starts_with($model, 'gemini')) {
            // ✅ OCTANE BEST PRACTICE: Use config() instead of env() here.
            $apiKey = config('services.gemini.key');
            if (!$apiKey) throw new \Exception('Gemini API Key is not set in config/services.php.');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            $payload = [
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => [ 'temperature' => $jsonMode ? 0.5 : 0.7, 'maxOutputTokens' => $jsonMode ? 1200 : 512, 'responseMimeType' => $jsonMode ? 'application/json' : 'text/plain', ],
            ];
            $response = Http::timeout(100)->post($url, $payload);
            if (!$response->successful()) {
                Log::error('Gemini API request failed: ' . $response->body());
                throw new \Exception('Failed to get a response from Gemini.');
            }
            return [ 'content' => $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '', 'model_name' => 'Gemini 1.5 Flash', ];
        } elseif (str_starts_with($model, 'mistral')) {
            // ✅ OCTANE BEST PRACTICE: Use config() instead of env() here.
            $apiKey = config('services.mistral.key');
            if (!$apiKey) throw new \Exception('Mistral API Key is not set in config/services.php.');
            $payload = [ 'model' => $model, 'messages' => [['role' => 'user', 'content' => $prompt]], 'temperature' => $jsonMode ? 0.5 : 0.7, 'max_tokens' => $jsonMode ? 1200 : 512, ];
            if($jsonMode) { $payload['response_format'] = ['type' => 'json_object']; }
            $response = Http::timeout(100)->withToken($apiKey)->post(self::MISTRAL_CHAT_API_URL, $payload);
            if (!$response->successful()) {
                Log::error('Mistral API request failed: ' . $response->body());
                throw new \Exception('Failed to get a response from Mistral AI.');
            }
            return [ 'content' => $response->json()['choices'][0]['message']['content'] ?? '', 'model_name' => $model, ];
        }
        throw new \Exception('Invalid AI model selected.');
    }

    private function gatherDataForSummary(array $filters): array
    {
        $period = $filters['period'] ?? '7d';
        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];

        $query = Order::query()->where('status', 'delivered');

        // Determine date range based on filter
        switch ($period) {
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
                if ($startDate && $endDate) {
                    $query->whereBetween('date_ordered', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
                }
                break;
            case '7d':
            default:
                $query->where('date_ordered', '>=', Carbon::now('Asia/Manila')->subDays(7)->startOfDay());
                break;
        }

        $recentSalesData = $query->join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                                ->select('products.generic_name', 'orders.quantity', DB::raw('orders.quantity * exclusive_deals.price as revenue'))
                                ->get();

        $totalRevenue = $recentSalesData->sum('revenue');
        $totalOrders = $recentSalesData->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $topProductData = $recentSalesData->groupBy('generic_name')
                                          ->map(fn($group) => $group->sum('revenue'))
                                          ->sortByDesc(fn($revenue) => $revenue)
                                          ->take(1);

        $topSellingProductInfo = [
            'name' => $topProductData->keys()->first() ?? 'N/A',
            'revenue' => $topProductData->first() ?? 0,
        ];

        $overallStats = [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'top_selling_product' => $topSellingProductInfo
        ];

        $inactiveProducts = Product::whereDoesntHave('orders', fn($q) => $q->where('date_ordered', '>=', Carbon::now()->subDays(14)))->limit(5)->pluck('generic_name');
        $lowStockProducts = Inventory::join('products', 'inventories.product_id', '=', 'products.id')->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))->groupBy('products.generic_name')->having('total_quantity', '<=', 50)->orderBy('total_quantity', 'asc')->limit(5)->get();

        return [
            'current_date' => Carbon::now('Asia/Manila')->format('Y-m-d'),
            'filter_period' => $period,
            'filter_start_date' => $startDate,
            'filter_end_date' => $endDate,
            'overall_stats' => $overallStats,
            'low_stock_products' => $lowStockProducts->toArray(),
            'inactive_products_last_14_days' => $inactiveProducts->toArray(),
        ];
    }

    private function createExecutiveSummaryPrompt(array $data, array $filters): string
    {
        $json_data = json_encode($data, JSON_PRETTY_PRINT);

        $periodDesc = match($filters['period'] ?? '7d') {
            'today' => 'for today',
            'this_week' => 'for this week',
            'this_month' => 'for this month',
            'this_year' => 'for this year',
            'custom' => 'for the period ' . ($filters['start_date'] ?? 'N/A') . ' to ' . ($filters['end_date'] ?? 'N/A'),
            default => 'for the last 7 days',
        };

        return <<<PROMPT
You are a world-class business analyst for an e-commerce company in the Philippines. Analyze the provided data and generate a concise "Executive Summary" for the CEO.
The data you are analyzing is filtered {$periodDesc}. Your analysis must reflect this time frame.

Your output MUST be a single, valid JSON object with this exact structure:
{
  "kpis": [
    { "label": "Key Metric Name", "value": "Value with units (e.g., ₱, %)", "trend": "up|down|stable|null" }
  ],
  "anomalies": [
    { "type": "positive|negative|warning", "message": "A concise description of the anomaly." }
  ],
  "recommendations": [
    { "message": "A short, actionable recommendation." }
  ]
}

- **kpis**: Generate exactly 3 KPIs based on the `overall_stats` section of the data: Total Revenue, Average Order Value, and Top-Selling Product.
- **anomalies**: Detect significant events or deviations. For example, if revenue is very high or very low.
- **recommendations**: Provide 1-2 strategic, actionable recommendations based on the entire dataset.
- Use professional business English. All monetary values are in Philippine Peso (PHP), format them like '₱1,234,567.89'.
- For the "trend" value in KPIs, you do not have comparison data, so set it to "null".
- The `Top-Selling Product` KPI label should include the product name and its revenue. Example: "Top Seller: Losartan (₱129,080)"

Here is the business data:
$json_data
PROMPT;
    }

    private function createChartAnalysisPrompt(array $chartData): string
    {
        $promptParts = [];
        foreach ($chartData as $chartId => $data) {
            $promptParts[] = "Chart '{$data['name']}':";
            $labels = json_encode($data['labels']);
            $values = json_encode($data['values']);
            $promptParts[] = "Labels: {$labels}";
            $promptParts[] = "Data: {$values}\n";
        }
        $fullData = implode("\n", $promptParts);
        return "As a business analytics expert, review the given chart data. Identify key trends, anomalies, and provide a single, concise paragraph (3-7 sentences) of actionable insights for a CEO. Focus on the business implications rather than just stating the numbers. Use Philippine Peso (₱) for currency values. Data: \n{$fullData}";
    }

    private function getDefaultSummaryStructure(string $errorMessage): array
    {
        return [
            'kpis' => [['label' => 'System Status', 'value' => 'Error', 'trend' => 'stable']],
            'anomalies' => [['type' => 'negative', 'message' => $errorMessage]],
            'recommendations' => [['message' => 'Please check the system logs or try again later.']]
        ];
    }

    public function getInventoryLevels($locationId = null)
    {
        $query = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'));

        // If a locationId is provided and is not null/empty, filter the query.
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

    public function getFilteredDeductedQuantities($year, $month, $locationId = null)
    {
        $deductedQuery = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->where('orders.status', 'delivered')
            ->whereYear('orders.updated_at', $year)
            ->whereMonth('orders.updated_at', $month);

        if ($locationId) {
            // This assumes a relationship exists that can link an order to a location.
            // A more direct link might be needed if this is too slow or inaccurate.
            $deductedQuery->whereHas('exclusiveDeal.product.inventories', function($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }

        $deductedQuantities = $deductedQuery->select(
                'products.generic_name',
                DB::raw('SUM(orders.quantity) as total_deducted')
            )
            ->groupBy('products.generic_name')
            ->orderBy('total_deducted', 'DESC')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $deductedQuantities->pluck('generic_name'),
            'deductedData' => $deductedQuantities->pluck('total_deducted')
        ]);
    }

    public function getRevenueData($period, $year, $month = null, $week = null)
    {
        if (!in_array($period, ['day', 'week', 'month', 'year'])) {
            return response()->json(['error' => 'Invalid period'], 400);
        }

        $query = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->where('orders.status', 'delivered')
            ->whereYear('orders.date_ordered', $year);

        switch ($period) {
            case 'day':
                if (!$month) return response()->json(['error' => 'Month required for daily data'], 400);
                $query->whereMonth('orders.date_ordered', $month)
                    ->select(DB::raw('DAY(orders.date_ordered) as period_value'), DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue'))
                    ->groupBy('period_value')->orderBy('period_value');
                break;
            case 'week':
                 if (!$month) return response()->json(['error' => 'Month required for weekly data'], 400);
                 $query->whereMonth('orders.date_ordered', $month)
                    ->select(DB::raw('WEEK(orders.date_ordered, 1) - WEEK(DATE_FORMAT(orders.date_ordered, "%Y-%m-01"), 1) + 1 as period_value'), DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue'))
                    ->groupBy('period_value')->orderBy('period_value');
                break;
            case 'month':
                $query->select(DB::raw('MONTH(orders.date_ordered) as period_value'), DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue'))
                    ->groupBy('period_value')->orderBy('period_value');
                break;
            case 'year':
                 $query->select(DB::raw('YEAR(orders.date_ordered) as period_value'), DB::raw('SUM(orders.quantity * exclusive_deals.price) as total_revenue'))
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
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $labels[] = date('M j', mktime(0, 0, 0, $month, $day, $year));
                    $values[] = $valueMap[$day] ?? 0;
                }
                break;
            case 'week':
                $date = new \DateTime("$year-$month-01");
                $weeksInMonth = ceil(($date->format('t') + $date->format('N')) / 7);
                for ($weekNum = 1; $weekNum <= $weeksInMonth; $weekNum++) {
                    $labels[] = "Week " . $weekNum;
                    $values[] = $valueMap[$weekNum] ?? 0;
                }
                break;
            case 'month':
                $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                for ($monthNum = 1; $monthNum <= 12; $monthNum++) {
                    $labels[] = $monthNames[$monthNum - 1];
                    $values[] = $valueMap[$monthNum] ?? 0;
                }
                break;
            case 'year':
                $startYear = Order::min(DB::raw('YEAR(date_ordered)')) ?? $year;
                for ($y = $startYear; $y <= $year; $y++) {
                    $labels[] = $y;
                    $values[] = $valueMap[$y] ?? 0;
                }
                break;
        }

        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    public function getTrendingProducts(Request $request)
    {
        // Suppress ONLY_FULL_GROUP_BY errors for this complex query
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $seasonFilter = $request->input('season', 'all');
        $today = Carbon::now('Asia/Manila');
        $startOfHistory = $today->copy()->subYears(2)->startOfYear(); // Use up to 2 years of data

        // 1. Get all relevant historical sales data in one query
        $historicalData = Order::where('status', 'delivered')
            ->where('date_ordered', '>=', $startOfHistory)
            ->join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.generic_name',
                'products.season_peak',
                DB::raw('YEAR(orders.date_ordered) as year'),
                DB::raw('MONTH(orders.date_ordered) as month'),
                DB::raw('SUM(orders.quantity) as total_quantity')
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

            $currentSales = $productData->where('year', $today->year)->where('month', '>=', $today->month-1)->where('month', '<=', $today->month)->sum('total_quantity') ?? 0;
            $m2_sales = $productData->where('year', $today->copy()->subMonth()->year)->where('month', $today->copy()->subMonth()->month)->sum('total_quantity') ?? 0;
            $m3_sales = $productData->where('year', $today->copy()->subMonths(2)->year)->where('month', '>=', $today->copy()->subMonths(2)->month)->where('month', '<=', $today->copy()->subMonths(1)->month)->sum('total_quantity') ?? 0;
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

            // --- NEW LOGIC FOR PERCENTAGE AND STATUS TEXT ---
            $percentageChange = 0;
            if ($currentSales > 0) {
                $percentageChange = (($finalPrediction - $currentSales) / $currentSales) * 100;
            } elseif ($finalPrediction > 0) {
                $percentageChange = 100; // From 0 to something is a big jump
            }

            $statusText = '';
            if ($percentageChange >= 25) {
                $statusText = 'There is a strong potential for a significant increase in sales.';
            } elseif ($percentageChange > 5) {
                $statusText = 'A slight increase in sales is expected.';
            } elseif ($percentageChange < -20) {
                $statusText = 'There is a high possibility of a significant drop in sales.';
            } elseif ($percentageChange < -5) {
                $statusText = 'Sales may decrease in the coming month.';
            } else {
                $statusText = 'No significant change is expected in the sales forecast.';
            }


            $predictions[] = [
                'id' => $product->id,
                'generic_name' => $product->generic_name,
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
}