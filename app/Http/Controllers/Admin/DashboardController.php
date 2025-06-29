<?php

namespace App\Http\Controllers\Admin;

use DB;
use Cache;
use Auth;
use App\Models\Order;
use App\Models\Location;
use App\Models\Inventory;
use App\Models\Conversation;
use App\Models\SuperAdmin;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Gathers data, sends it to the Gemini API, and returns a structured AI-generated summary.
     */
    private function getAIGeneratedExecutiveSummary()
    {
        try {
            // Step 1: Gather all relevant data for the AI's context.
            $sevenDaysAgo = Carbon::now()->subDays(7);

            $recentSalesData = Order::where('status', 'delivered')
                ->where('date_ordered', '>=', $sevenDaysAgo)
                ->join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
                ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
                ->select(
                    'products.generic_name',
                    'orders.quantity',
                    DB::raw('orders.quantity * exclusive_deals.price as revenue'),
                    DB::raw('DATE(orders.date_ordered) as date')
                )
                ->orderBy('date_ordered', 'desc')
                ->get();

            $overallStats = [
                'total_revenue_last_7_days' => $recentSalesData->sum('revenue'),
                'total_orders_last_7_days' => $recentSalesData->count(),
                'average_order_value_last_7_days' => $recentSalesData->count() > 0 ? $recentSalesData->sum('revenue') / $recentSalesData->count() : 0,
            ];

            $inactiveProducts = Product::whereDoesntHave('orders', fn($q) => $q->where('date_ordered', '>=', Carbon::now()->subDays(14)))
                ->limit(5)->pluck('generic_name');

            $lowStockProducts = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
                ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
                ->groupBy('products.generic_name')
                ->having('total_quantity', '<=', 50)
                ->orderBy('total_quantity', 'asc')
                ->limit(5)->get();

            $dataForPrompt = [
                'current_date' => Carbon::now('Asia/Manila')->format('Y-m-d'),
                'upcoming_season' => match (Carbon::now()->addMonth()->month) { 3,4,5 => 'Summer', 6,7,8,9,10,11 => 'Rainy', default => 'Neutral' },
                'overall_stats_last_7_days' => $overallStats,
                'recent_sales_details' => $recentSalesData->toArray(),
                'low_stock_products' => $lowStockProducts->toArray(),
                'inactive_products_last_14_days' => $inactiveProducts->toArray(),
            ];

            // Step 2: Engineer the prompt for the Gemini API.
            $prompt = $this->createGeminiPrompt($dataForPrompt);
            $geminiApiKey = env('GEMINI_API_KEY');

            if (!$geminiApiKey) {
                Log::error('Gemini API Key is not set.');
                return $this->getDefaultSummaryStructure('Gemini API Key is missing.');
            }

            $geminiApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiApiKey}";

            // Step 3: Call the Gemini API.
            $response = Http::post($geminiApiUrl, [
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => 0.6,
                    'topP' => 0.9,
                    'maxOutputTokens' => 2048,
                    'responseMimeType' => 'application/json', // Instruct Gemini to respond with JSON
                ],
            ]);

            if ($response->failed()) {
                Log::error('Gemini API request failed: ' . $response->body());
                return $this->getDefaultSummaryStructure('Failed to get a response from the AI.');
            }

            // Step 4: Decode the JSON response.
            $result = json_decode($response->json()['candidates'][0]['content']['parts'][0]['text'], true);

            if (!is_array($result) || !isset($result['kpis']) || !isset($result['anomalies']) || !isset($result['recommendations'])) {
                Log::warning('Gemini response was not in the expected JSON format.', ['response' => $response->body()]);
                return $this->getDefaultSummaryStructure('AI response was not in the expected format.');
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Exception in getAIGeneratedExecutiveSummary: ' . $e->getMessage());
            return $this->getDefaultSummaryStructure('An error occurred while generating the summary.');
        }
    }

    private function createGeminiPrompt(array $data): string
    {
        $json_data = json_encode($data, JSON_PRETTY_PRINT);

        return <<<PROMPT
        You are a world-class business analyst for an e-commerce company based in the Philippines. Your task is to analyze the provided business data and generate a concise "Executive Summary" for the CEO.

        **Instructions:**
        1.  Analyze the JSON data provided below.
        2.  Your entire output MUST be a single, valid JSON object. Do not include any text or markdown before or after the JSON object.
        3.  The JSON object must follow this exact structure:
            {
              "kpis": [
                { "label": "Key Metric Name", "value": "Value with units (e.g., ₱, %)", "trend": "up|down|stable" }
              ],
              "anomalies": [
                { "type": "positive|negative|warning", "message": "A concise description of the anomaly." }
              ],
              "recommendations": [
                { "message": "A short, actionable recommendation." }
              ]
            }
        4.  **KPIs:** Identify 2-3 of the most critical Key Performance Indicators from the data. The 'trend' should be based on a logical comparison.
        5.  **Anomalies:** Detect significant events or deviations. Prioritize the most impactful anomalies.
        6.  **Recommendations:** Provide 1-2 strategic, actionable recommendations based on your analysis.
        7.  Use clear, direct, professional business English. All monetary values are in Philippine Peso (PHP).

        **Business Data:**
        $json_data
        PROMPT;
    }

    private function getDefaultSummaryStructure(string $errorMessage): array
    {
        return [
            'kpis' => [
                ['label' => 'System Status', 'value' => 'Error', 'trend' => 'stable']
            ],
            'anomalies' => [
                ['type' => 'negative', 'message' => $errorMessage]
            ],
            'recommendations' => [
                ['message' => 'Please check the system logs or try again later.']
            ]
        ];
    }

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

        $totalOrders = Order::where('status', 'delivered')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $mostSoldProducts = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
            ->where('orders.status', 'delivered')
            ->groupBy('products.generic_name')
            ->orderBy('total_quantity', 'DESC')
            ->limit(6)->get();

        $labels = $mostSoldProducts->pluck('generic_name')->toArray();
        $data = $mostSoldProducts->pluck('total_quantity')->toArray();

        $lowSoldProductsQuery = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
            ->where('orders.status', 'delivered')
            ->groupBy('products.generic_name')
            ->having('total_quantity', '<=', 10)
            ->orderBy('total_quantity', 'ASC')
            ->limit(6)->get();

        $lowSoldLabels = $lowSoldProductsQuery->pluck('generic_name')->toArray();
        $lowSoldData = $lowSoldProductsQuery->pluck('total_quantity')->toArray();

        $moderateSoldProducts = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_quantity'))
            ->where('orders.status', 'delivered')
            ->groupBy('products.generic_name')
            ->having('total_quantity', '>', 10)
            ->having('total_quantity', '<=', 50)
            ->orderBy('total_quantity', 'DESC')
            ->limit(6)->get();

        $moderateSoldLabels = $moderateSoldProducts->pluck('generic_name')->toArray();
        $moderateSoldData = $moderateSoldProducts->pluck('total_quantity')->toArray();

        $deductedQuantitiesInitial = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->where('orders.status', 'delivered')
            ->whereYear('orders.updated_at', date('Y'))
            ->whereMonth('orders.updated_at', date('n'))
            ->select('products.generic_name', DB::raw('SUM(orders.quantity) as total_deducted'))
            ->groupBy('products.generic_name')
            ->orderBy('total_deducted', 'DESC')
            ->limit(10)->get();

        $deductedLabels = $deductedQuantitiesInitial->pluck('generic_name')->toArray();
        $deductedData = $deductedQuantitiesInitial->pluck('total_deducted')->toArray();

        $lowStockProducts = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
            ->groupBy('products.generic_name')
            ->having('total_quantity', '<=', 50)->get();

        $totalRevenue = Order::join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->where('orders.status', 'delivered')
            ->sum(DB::raw('orders.quantity * exclusive_deals.price'));

        $availableYears = Order::select(DB::raw('YEAR(date_ordered) as year'))->distinct()->orderBy('year', 'DESC')->pluck('year')
            ->merge(Inventory::select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year'))
            ->unique()->sortDesc()->values();

        $orderStatusCounts = Order::select('status', DB::raw('COUNT(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray();

        $totalSales = Order::where('status', 'delivered')
            ->join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->sum(DB::raw('orders.quantity * exclusive_deals.price'));

        $deliveredOrdersCount = Order::where('status', 'delivered')->count();

        $averageOrderValue = $deliveredOrdersCount > 0 ? $totalSales / $deliveredOrdersCount : 0;

        $executiveSummary = $this->getAIGeneratedExecutiveSummary();

        return view('admin.dashboard', compact(
            'locations', 'unreadMessagesAdmin', 'unreadMessagesSuperAdmin', 'unreadMessagesStaff', 'adminsidebar_counter',
            'currentUser', 'totalOrders', 'pendingOrders', 'cancelledOrders', 'labels', 'data', 'lowSoldLabels',
            'lowSoldData', 'moderateSoldLabels', 'moderateSoldData', 'deductedLabels', 'deductedData',
            'lowStockProducts', 'totalRevenue', 'availableYears', 'orderStatusCounts', 'averageOrderValue', 'executiveSummary'
        ));
    }


    public function getInventoryLevels($year, $month, $locationId = null)
    {
        $query = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.generic_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
            ->whereYear('inventories.created_at', $year)
            ->whereMonth('inventories.created_at', $month);

        if ($locationId && $locationId !== 'null') {
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

        if ($locationId && $locationId !== 'null') {
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
                $availableYears = Order::select(DB::raw('YEAR(date_ordered) as year'))->distinct()->orderBy('year')->pluck('year')->toArray();
                foreach ($availableYears as $availYear) {
                    $labels[] = $availYear;
                    $values[] = $valueMap[$availYear] ?? 0;
                }
                break;
        }

        $total = array_sum($values);
        $average = count($values) > 0 ? $total / count($values) : 0;

        return response()->json(['labels' => $labels, 'values' => $values, 'total' => $total, 'average' => $average]);
    }

    public function getTrendingProducts(Request $request)
    {
        DB::statement("SET SESSION sql_mode=''");

        $season = $request->input('season', 'all');
        $year = $request->input('year', now()->year);
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $currentSeason = match ($currentMonth) {
            3, 4, 5 => 'tag-init',
            6, 7, 8, 9, 10, 11 => 'tag-ulan',
            default => 'all-year',
        };

        $historicalData = Order::where('status', 'delivered')
            ->whereYear('date_ordered', $year)
            ->join('exclusive_deals', 'orders.exclusive_deal_id', '=', 'exclusive_deals.id')
            ->join('products', 'exclusive_deals.product_id', '=', 'products.id')
            ->select('products.id', 'products.generic_name', 'products.season_peak', DB::raw('MONTH(orders.date_ordered) as month'), DB::raw('YEAR(orders.date_ordered) as year'), DB::raw('SUM(orders.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.generic_name', 'products.season_peak', 'month', 'year')
            ->get()->groupBy('id');

        $products = Product::all()->map(function ($product) use ($historicalData, $currentMonth, $currentYear, $currentSeason) {
            $productMonthlyData = $historicalData->get($product->id);
            $currentQuantity = $productMonthlyData ?->where('month', $currentMonth)->where('year', $currentYear)->sum('total_quantity') ?? 0;

            $monthlyAverages = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthlyAverages[$m] = $productMonthlyData ?->where('month', $m)->avg('total_quantity') ?? 0;
            }

            $nextMonth = ($currentMonth % 12) + 1;
            $nextSeason = match ($nextMonth) {
                3, 4, 5 => 'tag-init',
                6, 7, 8, 9, 10, 11 => 'tag-ulan',
                default => 'all-year',
            };

            $nextMonthAvg = $monthlyAverages[$nextMonth];
            if ($product->season_peak === $nextSeason) {
                $nextMonthAvg *= 1.3;
            } elseif ($product->season_peak !== 'all-year' && $product->season_peak !== $nextSeason) {
                $nextMonthAvg *= 0.8;
            }

            $historicalOverallAverage = collect($monthlyAverages)->avg();
            $maxPossible = collect($monthlyAverages)->max();
            $predictionPercent = $maxPossible > 0 ? min(100, round(($nextMonthAvg / $maxPossible) * 100, 2)) : 0;
            $trendScore = ($currentQuantity * 0.4) + ($nextMonthAvg * 0.6);
            if ($product->season_peak === $currentSeason) {
                $trendScore *= 1.2;
            }

            return [
                'id' => $product->id, 'generic_name' => $product->generic_name, 'season_peak' => $product->season_peak,
                'current_sales' => round($currentQuantity), 'next_month_prediction' => round($nextMonthAvg, 2),
                'historical_avg' => round($historicalOverallAverage, 2), 'prediction_percent' => $predictionPercent,
                'trend_score' => round($trendScore, 2),
            ];
        });

        $filteredProducts = $products;
        if ($season !== 'all') {
            $filteredProducts = $products->filter(fn($product) => $product['season_peak'] === $season);
        }

        return response()->json([
            'current_season' => $currentSeason, 'current_month' => $currentMonth, 'current_year' => $currentYear,
            'trending_products' => $filteredProducts->sortByDesc('trend_score')->take(10)->values(),
            'predicted_peaks' => $filteredProducts->sortByDesc('prediction_percent')->take(6)->values()
        ]);
    }

    private function compareAndChooseAnalysis(array $analyses): array
    {
        $bestAnalysis = '';
        $bestScore = -1;
        $bestModel = 'Unknown';
        $keywords = ['trends', 'insights', 'recommendations', 'revenue', 'products', 'inventory'];

        foreach ($analyses as $source => $analysis) {
            $score = 0;
            $score += strlen($analysis);

            foreach ($keywords as $keyword) {
                if (stripos($analysis, $keyword) !== false) $score += 50;
            }
            if (!empty($analysis) && !str_contains(strtolower($analysis), 'error') && !str_contains(strtolower($analysis), 'failed')) {
                $score += 100;
            }
            if ($source === 'gemini') $score += 20;

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestAnalysis = $analysis;
                $bestModel = $source === 'gemini' ? 'Gemini 1.5 Flash' : 'OpenAI GPT-4o Mini';
            }
        }
        return ['analysis' => $bestAnalysis ?: 'Unable to generate a conclusive analysis from available AI models.', 'model' => $bestModel];
    }

    public function analyzeChartsWithAI(Request $request)
    {
        $chartData = $request->json()->all();
        $promptParts = [];

        foreach ($chartData as $chartName => $data) {
            $promptParts[] = "The following is data for the '{$chartName}' chart:";
            $promptParts[] = "Values: " . json_encode($data['values']);
            $promptParts[] = "Labels: " . json_encode($data['labels']);
            if (isset($data['filters'])) {
                $promptParts[] = "Filters used: " . json_encode($data['filters']);
            }
            $promptParts[] = "";
        }

        $basePrompt = "As a business analytics expert, review the given chart data. Identify key trends, anomalies, and provide actionable insights or recommendations for the superadmin. Ensure that the analysis is accurate and easy to understand. Avoid using technical jargon. Limit your response to a summary paragraph (at least 3 sentences, no more than 7).";
        $fullPrompt = $basePrompt . "\n\nData from my charts.\n" . implode("\n", $promptParts);

        $geminiAnalysis = 'Could not get a response from Gemini.';
        $openaiAnalysis = 'Could not get a response from OpenAI.';

        try {
            $geminiApiKey = env('GEMINI_API_KEY');
            $geminiApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiApiKey}";
            $geminiResponse = Http::post($geminiApiUrl, [
                'contents' => [['role' => 'user', 'parts' => [['text' => $fullPrompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'topP' => 0.95, 'topK' => 64, 'maxOutputTokens' => 512, 'responseMimeType' => 'text/plain'],
            ]);
            if ($geminiResponse->successful()) {
                $geminiAnalysis = $geminiResponse->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Could not generate an analysis from Gemini.';
            } else {
                Log::error('Gemini API Error: ' . $geminiResponse->body());
                $geminiAnalysis = 'Error from Gemini API: ' . $geminiResponse->status();
            }
        } catch (\Exception $e) {
            Log::error('Exception during Gemini API call: ' . $e->getMessage());
            $geminiAnalysis = 'Exception with Gemini API: ' . $e->getMessage();
        }

        try {
            $openaiApiKey = env('OPENAI_API_KEY');
            $openaiApiUrl = "https://api.openai.com/v1/chat/completions";
            $openaiResponse = Http::withToken($openaiApiKey)->post($openaiApiUrl, [
                'model' => 'gpt-4o-mini', 'messages' => [['role' => 'user', 'content' => $fullPrompt]],
                'max_tokens' => 512, 'temperature' => 0.7,
            ]);
            if ($openaiResponse->successful()) {
                $openaiAnalysis = $openaiResponse->json()['choices'][0]['message']['content'] ?? 'Could not generate an analysis from OpenAI.';
            } else {
                Log::error('OpenAI API Error: ' . $openaiResponse->body());
                $openaiAnalysis = 'Error from OpenAI API: ' . $openaiResponse->status();
            }
        } catch (\Exception $e) {
            Log::error('Exception during OpenAI API call: ' . $e->getMessage());
            $openaiAnalysis = 'Exception with OpenAI API: ' . $e->getMessage();
        }

        $finalAnalysisData = $this->compareAndChooseAnalysis(['gemini' => $geminiAnalysis, 'openai' => $openaiAnalysis]);

        return response()->json($finalAnalysisData);
    }
}