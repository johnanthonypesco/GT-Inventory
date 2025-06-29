<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Location;
use App\Models\OcrInventoryLog;
use Carbon\Carbon;
use Exception; // It's good practice to import the base Exception class
use Illuminate\Validation\ValidationException;

class OcrInventoryController extends Controller
{
    /**
     * Handles the upload of a receipt image, extracts data using Gemini API,
     * and returns the structured data for confirmation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadReceipt(Request $request)
    {
        try {
            // 1. Validate the uploaded image
            $request->validate([
                'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $file = $request->file('receipt_image');
            if (!$file->isValid()) {
                return response()->json(['message' => 'File upload failed: ' . $file->getErrorMessage()], 400);
            }

            // 2. Prepare the image and API key for the Gemini API call
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType = $file->getMimeType();
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                Log::error('Gemini API Key is missing from .env file.');
                return response()->json(['message' => 'Server configuration error: API key is not set.'], 500);
            }

            // 3. Define the precise JSON structure we want the AI to return.
            $jsonSchema = [
                'type' => 'OBJECT',
                'properties' => [
                    'data' => [
                        'type' => 'ARRAY',
                        'items' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'quantity' => ['type' => 'NUMBER', 'description' => 'The numeric quantity from the receipt.'],
                                'brand_name' => ['type' => 'STRING', 'description' => 'The exact brand name of the product from the receipt. Must be an empty string if blank.'],
                                'product_name' => ['type' => 'STRING', 'description' => 'The exact generic name of the product, including strength.'],
                                'form' => ['type' => 'STRING', 'description' => 'The form of the product (e.g., Tablet, Capsule, Syrup).'],
                                'batch_number' => ['type' => 'STRING', 'description' => 'The alphanumeric batch number.'],
                                'expiry_date' => ['type' => 'STRING', 'description' => 'The expiry date insimpleTriplet-MM-DD format.'],
                            ],
                            'required' => ['quantity', 'product_name', 'batch_number', 'expiry_date', 'brand_name']
                        ]
                    ]
                ]
            ];
            
            // 4. Create a very forceful and specific prompt for data extraction.
            $prompt = "You are a highly precise OCR system for pharmaceutical receipts.
                       Analyze the provided 'Vitalis Pharma Phil. Corp. Acknowledgement Receipt'.
                       Extract ALL product data from the table with 100% accuracy.
                       RULES:
                       1.  **Brand Name Column is CRITICAL**: You MUST extract the exact text from the 'Brand Name' column for every single row.
                       2.  If the 'Brand Name' cell for a row is BLANK on the receipt, you MUST return an empty string for the 'brand_name' field.
                       3.  **DO NOT GUESS**: Never copy the 'Generic Name' into the 'brand_name' field. The 'brand_name' must come ONLY from the 'Brand Name' column.
                       4.  **Generic Name**: Extract the exact text from the 'Generic Name' column.
                       5.  **Dates**: Convert all expiry dates to a strict YYYY-MM-DD format.
                       6.  **Output**: Return ONLY the JSON object. Do not add any introductory text.";


            // 5. Make the API call to Gemini for OCR
            $response = Http::timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}",
                [
                    "contents" => [["parts" => [["text" => $prompt], ["inline_data" => ["mime_type" => $mimeType, "data" => $imageData]]]]],
                    "safetySettings" => [
                        ["category" => "HARM_CATEGORY_HARASSMENT", "threshold" => "BLOCK_NONE"],
                        ["category" => "HARM_CATEGORY_HATE_SPEECH", "threshold" => "BLOCK_NONE"],
                        ["category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold" => "BLOCK_NONE"],
                        ["category" => "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold" => "BLOCK_NONE"]
                    ],
                    "generationConfig" => ["responseMimeType" => "application/json", "responseSchema" => $jsonSchema]
                ]
            );

            // 6. Process the API response
            if ($response->failed()) {
                Log::error('Gemini API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
                throw new Exception("The AI service failed to respond. Status: " . $response->status());
            }

            $geminiResult = $response->json();
            Log::debug("Gemini API Full Response", $geminiResult);

            if (empty($geminiResult['candidates'][0]['content']['parts'][0]['text'])) {
                $reason = $geminiResult['candidates'][0]['finishReason'] ?? 'unknown reason';
                throw new Exception("AI processing failed. This can happen if the image is unclear or the content was blocked. Reason: {$reason}");
            }

            $extractedJson = $geminiResult['candidates'][0]['content']['parts'][0]['text'];
            OcrInventoryLog::create(['raw_text' => $extractedJson]);

            $extractedData = json_decode($extractedJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON from AI: " . json_last_error_msg());
            }

            if (empty($extractedData['data'])) {
                throw new Exception("The AI successfully read the receipt but found no product data.");
            }

            // 7. Normalize the extracted data and get season peak
            $products = array_map(function($item) {
                $productName = $this->cleanProductName($item['product_name'] ?? '');
                $brandName = isset($item['brand_name']) && trim($item['brand_name']) !== '' ? trim($item['brand_name']) : null;
                // ***NEW***: Get the season peak prediction here to send to the form.
                $seasonPeak = $this->getSeasonPeakForProduct($productName);
                
                return [
                    'quantity' => (int)($item['quantity'] ?? 0),
                    'brand_name' => $brandName,
                    'product_name' => $productName,
                    'form' => $this->normalizeForm($item['form'] ?? 'Tablet'),
                    'batch_number' => $item['batch_number'] ?? '',
                    'expiry_date' => $this->normalizeDate($item['expiry_date'] ?? ''),
                    'strength' => $this->extractStrength($productName),
                    'season_peak' => $seasonPeak // Add to the response for the form
                ];
            }, $extractedData['data']);

            return response()->json([
                'status' => 'success',
                'message' => '✅ Data extracted successfully! Please review and save.',
                'data' => $products
            ]);

        } catch (Exception $e) {
            Log::error("OCR Processing Error", ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Saves the validated and confirmed inventory data to the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveInventory(Request $request)
    {
        try {
            // ***UPDATED***: Added season_peak to validation.
            $validated = $request->validate([
                'products' => 'required|array|min:1',
                'products.*.product_name' => 'required|string|max:255',
                'products.*.brand_name' => 'nullable|string|max:255',
                'products.*.form' => 'required|string|max:50',
                'products.*.strength' => 'required|string|max:50',
                'products.*.season_peak' => 'required|string|in:Tag-init,Tag-ulan,All-Year',
                'products.*.batch_number' => 'required|string|max:50',
                'products.*.expiry_date' => 'required|date|after_or_equal:today',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.location' => 'required|string|max:255',
            ]);

            $results = ['created' => [], 'updated' => [], 'errors' => []];

            foreach ($validated['products'] as $data) {
                try {
                    $productName = $this->cleanProductName($data['product_name']);
                    $strength = !empty($data['strength']) ? $data['strength'] : $this->extractStrength($productName);
                    $form = !empty($data['form']) ? $this->normalizeForm($data['form']) : 'Tablet';
                    
                    // ***UPDATED***: Use value from form for season_peak.
                    $product = Product::updateOrCreate(
                        [
                            'generic_name' => $productName,
                            'strength' => $strength,
                            'form' => $form,
                        ],
                        [
                            'brand_name' => $data['brand_name'] ?? null,
                            'season_peak' => $data['season_peak'], // Use user-confirmed value
                            'is_auto_created' => true
                        ]
                    );

                    if ($product->wasRecentlyCreated) {
                        $results['created'][] = $product->generic_name;
                    } else {
                        $results['updated'][] = $product->generic_name;
                    }

                    $location = Location::firstOrCreate(['province' => $data['location']]);

                    Inventory::create([
                        'product_id' => $product->id,
                        'batch_number' => $data['batch_number'],
                        'expiry_date' => $data['expiry_date'],
                        'quantity' => $data['quantity'],
                        'location_id' => $location->id
                    ]);

                } catch (Exception $e) {
                    $productNameForError = $data['product_name'] ?? 'Unknown';
                    $results['errors'][] = ['product' => $productNameForError, 'error' => $e->getMessage()];
                    Log::error("Failed to save product inventory: " . $e->getMessage(), ['product_data' => $data]);
                }
            }

            $response = [
                'status' => empty($results['errors']) ? 'success' : 'partial',
                'message' => 'Inventory processed successfully.',
                'results' => $results
            ];

            if (!empty($results['errors'])) {
                $response['message'] .= ' with ' . count($results['errors']) . ' error(s).';
            }

            return response()->json($response);

        } catch (ValidationException $e) {
            Log::error("Inventory Save Validation Error", ['error' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Please correct the errors and try again.',
                'errors' => $e->errors()
            ], 422); // 422 is the standard code for validation errors
        } catch (Exception $e) {
            Log::error("General Inventory Save Error", ['error' => $e->getMessage(),'trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => 'A critical error occurred: ' . $e->getMessage()], 500);
        }
    }
    
    // ... (rest of the helper methods remain the same) ...

    public function getLocations()
    {
        $locations = Location::orderBy('province')->pluck('province')->toArray();
        if (empty($locations)) {
            $locations = ['Tarlac', 'Pampanga', 'Pangasinan', 'Manila'];
        }
        return response()->json(['locations' => $locations]);
    }

    private function getSeasonPeakForProduct(string $productName): string
    {
        try {
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                Log::warning('Gemini API Key is missing, cannot determine season peak.');
                return 'All-Year'; // Default value
            }

            $prompt = "As a pharmaceutical expert in the Philippines, for a medicine with the generic name '{$productName}', is its peak demand during the 'Tag-init' (hot/dry season), 'Tag-ulan' (rainy season), or is it needed 'All-Year'? Respond with only one of these three options: Tag-init, Tag-ulan, or All-Year.";

            $response = Http::timeout(20)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}",
                [
                    "contents" => [["parts" => [["text" => $prompt]]]],
                    "safetySettings" => [
                        ["category" => "HARM_CATEGORY_HARASSMENT", "threshold" => "BLOCK_NONE"],
                        ["category" => "HARM_CATEGORY_HATE_SPEECH", "threshold" => "BLOCK_NONE"],
                        ["category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold" => "BLOCK_NONE"],
                        ["category" => "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold" => "BLOCK_NONE"]
                    ]
                ]
            );

            if ($response->failed()) {
                Log::warning('Gemini API call for season peak failed.', ['status' => $response->status()]);
                return 'All-Year';
            }

            $result = $response->json();
            if (empty($result['candidates'][0]['content']['parts'][0]['text'])) {
                Log::warning('Gemini response for season peak was empty.');
                return 'All-Year';
            }

            $responseText = trim($result['candidates'][0]['content']['parts'][0]['text']);

            $lowerResponse = strtolower($responseText);
            if (str_contains($lowerResponse, 'tag-init') || str_contains($lowerResponse, 'dry') || str_contains($lowerResponse, 'summer') || str_contains($lowerResponse, 'hot')) {
                return 'Tag-init';
            } elseif (str_contains($lowerResponse, 'tag-ulan') || str_contains($lowerResponse, 'rainy') || str_contains($lowerResponse, 'wet')) {
                return 'Tag-ulan';
            } else {
                return 'All-Year';
            }

        } catch (Exception $e) {
            Log::error('Error in getSeasonPeakForProduct: ' . $e->getMessage());
            return 'All-Year'; // Default value on any exception
        }
    }

    private function normalizeForm(string $form): string
    {
        $form = strtolower(trim($form));
        if (in_array($form, ['oral', 'tablet', 'capsule', 'pill', 'cap', 'tab'])) return 'Tablet';
        if (in_array($form, ['injection', 'injectable', 'iv', 'vial'])) return 'Injection';
        if (in_array($form, ['syrup', 'suspension', 'liquid'])) return 'Syrup';
        if (in_array($form, ['ointment', 'cream', 'gel'])) return 'Ointment';
        return 'Tablet';
    }

    private function normalizeDate(string $date): string
    {
        if (empty(trim($date))) { return '1970-01-01'; }
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (Exception $e) {
            Log::warning("Could not parse date format: {$date}. Falling back.");
            return '1970-01-01';
        }
    }

    private function cleanProductName(string $name): string
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    private function extractStrength(string $productName): string
    {
        $pattern = '/(\d+(\.\d+)?\s*(mg|mcg|g|ml|%|iu)(\s*\/\s*\d*\.?\d*\s*ml)?)/i';
        if (preg_match($pattern, $productName, $matches)) {
            return trim($matches[0]);
        }
        return 'N/A';
    }
}

// sakses