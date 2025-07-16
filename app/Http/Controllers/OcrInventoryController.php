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
use Exception;
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
                                'product_name' => ['type' => 'STRING', 'description' => 'The generic name of the product, including strength, corrected and standardized for any misspellings, abbreviations, or unclear handwriting.'],
                                'form' => ['type' => 'STRING', 'description' => 'The standard pharmaceutical form (e.g., Tablet, Injection, Syrup), intelligently corrected and normalized from any misspellings, extra letters, or abbreviations.'],
                                'batch_number' => ['type' => 'STRING', 'description' => 'The alphanumeric batch number exactly as seen.'],
                                'expiry_date' => ['type' => 'STRING', 'description' => 'The expiry date in YYYY-MM-DD format.'],
                            ],
                            'required' => ['quantity', 'product_name', 'batch_number', 'expiry_date', 'brand_name', 'form']
                        ]
                    ]
                ]
            ];
            
            // 4. Create a very forceful and specific prompt for data extraction with AI interpretation.
            $prompt = "You are a highly intelligent and detail-oriented OCR system specializing in pharmaceutical receipts.
                        Your primary goal is to analyze the provided 'Vitalis Pharma Phil. Corp. Acknowledgement Receipt' and extract ALL product data from the table with exceptional accuracy, interpreting and correcting text where appropriate to provide clean, standardized data.

                        RULES FOR EXTRACTION AND INTELLIGENT INTERPRETATION:
                        1.  **Quantity**: Extract the exact numeric quantity.
                        2.  **Brand Name**: Extract the *exact text* from the 'Brand Name' column. If the cell is BLANK on the receipt, return an empty string. DO NOT GUESS or infer from generic name.
                        3.  **Generic Name**: Extract the generic name. If there are any misspellings, extra letters, or abbreviations (e.g., 'Paracetmol', 'Biaxin XLs'), use your expert pharmaceutical knowledge to provide the most common and correct spelling/full name (e.g., 'Paracetamol', 'Biaxin XL').
                        4.  **Form**: Extract the product's form. This is critical. If there are misspellings (e.g., 'injction'), extra letters (e.g., 'injectables'), common abbreviations (e.g., 'tab', 'inj', 'syr'), or unclear handwriting, you MUST interpret and provide the **standard, singular pharmaceutical form** (e.g., 'Tablet', 'Capsule', 'Syrup', 'Injection', 'Ointment'). If it's truly unidentifiable, respond with 'N/A'.
                        5.  **Batch Number**: Extract the alphanumeric batch number *exactly* as written.
                        6.  **Expiry Date**: Extract the expiry date and convert it to a strict YYYY-MM-DD format. Ensure correct year and month interpretation.
                        7.  **Output**: Return ONLY the JSON object conforming strictly to the specified schema. Do not add any introductory or concluding text, or conversational filler. Ensure all required fields are present and accurately populated based on these rules.";

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
                $form = $this->normalizeForm($item['form'] ?? 'N/A');
                $seasonPeak = $this->getSeasonPeakForProduct($productName);
                
                return [
                    'quantity' => (int)($item['quantity'] ?? 0),
                    'brand_name' => $brandName,
                    'product_name' => $productName,
                    'form' => $form,
                    'batch_number' => $item['batch_number'] ?? '',
                    'expiry_date' => $this->normalizeDate($item['expiry_date'] ?? ''),
                    'strength' => $this->extractStrength($productName),
                    'season_peak' => $seasonPeak
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
     * Handles existing batch numbers by updating their quantity.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveInventory(Request $request)
    {
        try {
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

            // Results array to track created items, duplicates, and errors
            $results = [
                'inventory_created' => [],
                'duplicates' => [], // For items that were skipped
                'errors' => []
            ];

            foreach ($validated['products'] as $data) {
                try {
                    // Find or create the product and location (this part is correct)
                    $productName = $this->cleanProductName($data['product_name']);
                    $strength = !empty($data['strength']) ? $data['strength'] : $this->extractStrength($productName);
                    $form = !empty($data['form']) ? $this->normalizeForm($data['form']) : 'N/A';
                    
                    $product = Product::firstOrCreate(
                        [
                            'generic_name' => $productName,
                            'strength' => $strength,
                            'form' => $form,
                        ],
                        [
                            'brand_name' => $data['brand_name'] ?? null,
                            'season_peak' => $data['season_peak'],
                            'is_auto_created' => true
                        ]
                    );

                    $location = Location::firstOrCreate(['province' => $data['location']]);

                    // --- THIS IS THE PERFECT LOGIC FOR YOUR RULES ---
                    // 1. Check if the inventory item already exists for the specific location.
                    $inventory = Inventory::where('product_id', $product->id)
                                          ->where('batch_number', $data['batch_number'])
                                          ->where('location_id', $location->id) // Checks the specific location
                                          ->first();

                    if ($inventory) {
                        // 2. If it exists (for this location), REJECT it and add to report.
                        $results['duplicates'][] = "{$product->generic_name} (Batch: {$data['batch_number']}) at {$location->province}";
                        
                        // 3. Skip to the next item in the loop. DO NOT save or update.
                        continue;
                    }

                    // 4. If it does NOT exist (for this location), create the new inventory record.
                    Inventory::create([
                        'product_id' => $product->id,
                        'batch_number' => $data['batch_number'],
                        'expiry_date' => $data['expiry_date'],
                        'quantity' => $data['quantity'],
                        'location_id' => $location->id
                    ]);
                    
                    $results['inventory_created'][] = "{$product->generic_name} (Batch: {$data['batch_number']}) at {$location->province}";

                } catch (Exception $e) {
                    $productNameForError = $data['product_name'] ?? 'Unknown';
                    $results['errors'][] = ['product' => $productNameForError, 'error' => $e->getMessage()];
                    Log::error("Failed to save product inventory: " . $e->getMessage(), ['product_data' => $data]);
                }
            }

            $response = [
                'status' => (empty($results['errors']) && empty($results['duplicates'])) ? 'success' : 'partial',
                'message' => 'Inventory processing complete.',
                'results' => $results
            ];

            return response()->json($response);

        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Please correct the errors.', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'A critical error occurred: ' . $e->getMessage()], 500);
        }
    }
    
    public function getLocations()
    {
        $locations = Location::orderBy('province')->pluck('province')->toArray();
        if (empty($locations)) {
            // Provide a default list if the database is empty
            $locations = ['Tarlac', 'Pampanga', 'Pangasinan', 'Manila', 'Baguio']; 
        }
        return response()->json(['locations' => $locations]);
    }

    private function getSeasonPeakForProduct(string $productName): string
    {
        try {
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                Log::warning('Gemini API Key is missing, cannot determine season peak.');
                return 'All-Year';
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
            $responseText = trim($result['candidates'][0]['content']['parts'][0]['text'] ?? '');

            if (empty($responseText)) {
                 Log::warning('Gemini response for season peak was empty.');
                return 'All-Year';
            }
            
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
            return 'All-Year';
        }
    }

    private function normalizeForm(string $form): string
    {
        $form = strtolower(trim($form));
        $map = [
            'Tablets' => ['tablet', 'tablets', 'pill', 'pills', 'cap', 'caps', 'tab', 'tabs', 'table', 'tblt', 'tbl', 'tabl'],
            'Injectables' => ['inj','inject','injection', 'injections', 'injectable', 'injectables', 'inject', 'iv', 'vial', 'vials', 'ampule', 'ampules', 'ampoule', 'ampoules', 'injec', 'injction', 'im', 'sc', 'subcut', 'intramuscular'],
            'Syrup' => ['syrup', 'syrups', 'suspension', 'suspensions', 'liquid', 'liquids', 'syr', 'syrupn', 'elixir', 'elixirs', 'mixture', 'mixtures', 'liq', 'lqd'],
            'Ointment' => ['ointment', 'ointments', 'cream', 'creams', 'gel', 'gels', 'oint', 'topical', 'top', 'creme', 'cremes', 'ung', 'salve', 'balm'],
            'Drops' => ['drops', 'gtts', 'droplets', 'eye drops', 'ear drops', 'nasal drops', 'opthalmic drops', 'otic drops', 'gt', 'drop'],
            'Solution' => ['solution', 'solutions', 'soln', 'sol', 'solutn', 'soltn', 'solns', 'solut', 'solvent'],
            'Powder' => ['powder', 'powders', 'pwd', 'pdr', 'granules', 'granule', 'grs', 'grnls'],
            'Aerosol' => ['aerosol', 'aerosols', 'inhaler', 'inhalers', 'puffer', 'puffers', 'mdpi', 'dpi', 'nebulizer', 'nebulizers'],
            'Suppository' => ['suppository', 'suppositories', 'supp', 'suppos', 'rectal', 'vaginal', 'pessary', 'pessaries'],
            'Patch' => ['patch', 'patches', 'transdermal', 'tds', 'td'],
            'Spray' => ['spray', 'sprays', 'mist', 'mists', 'nasal spray', 'oral spray'],
            'Oral' => ['o', 'or', 'orl', 'oral', 'po', 'by mouth', 'p.o.', 'per os'],
            'Film' => ['film', 'films', 'oral film', 'dissolving film', 'strip', 'strips'],
            'Lozenge' => ['lozenge', 'lozenges', 'troche', 'troches', 'pastille', 'pastilles'],
            'Gum' => ['gum', 'gums', 'chewing gum', 'medicated gum'],
            'Implant' => ['implant', 'implants', 'insert', 'inserts', 'pellet', 'pellets'],
            'Enema' => ['enema', 'enemas', 'rectal enema', 'fleetenema'],
            'Lotion' => ['lotion', 'lotions', 'liniment', 'liniments'],
            'Shampoo' => ['shampoo', 'shampoos', 'medicated shampoo'],
            'Foam' => ['foam', 'foams', 'medicated foam'],
            'Paste' => ['paste', 'pastes', 'dental paste'],
            'Gas' => ['gas', 'gasses', 'inhalation gas', 'medical gas'],
            'Disk' => ['disk', 'disks', 'disc', 'discs', 'diskette', 'diskettes'],
            'Wafer' => ['wafer', 'wafers', 'oral wafer'],
            'Powder for Injection' => ['powder for injection', 'lyophilized', 'lyo', 'lyophilizate'],
            'Emulsion' => ['emulsion', 'emulsions', 'emul', 'emulsn'],
            'Tincture' => ['tincture', 'tinctures', 'tinc', 'tinct'],
        ];

        foreach ($map as $standard => $variations) {
            if (in_array($form, $variations)) {
                return $standard;
            }
        }
        
        return 'N/A'; 
    }

    private function normalizeDate(string $date): string
    {
        if (empty(trim($date))) { return '1970-01-01'; } 
        try {
            $carbonDate = Carbon::parse($date);
            if ($carbonDate->year < 100) {
                $carbonDate->year += ($carbonDate->year <= (int)Carbon::now()->format('y') + 15) ? 2000 : 1900; 
            }
            return $carbonDate->format('Y-m-d');
        } catch (Exception $e) {
            Log::warning("Could not parse date format: {$date}. Falling back. Error: " . $e->getMessage());
            return '1970-01-01';
        }
    }

    private function cleanProductName(string $name): string
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    private function extractStrength(string $productName): string
    {
        $pattern = '/(\d+(\.\d+)?\s*(mg|mcg|g|ml|%|iu|units)(\s*\/\s*\d*\.?\d*\s*ml)?)/i';
        if (preg_match($pattern, $productName, $matches)) {
            return trim($matches[0]);
        }
        return 'N/A';
    }
}