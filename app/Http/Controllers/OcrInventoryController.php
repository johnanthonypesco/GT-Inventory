<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Location;
use App\Models\OcrInventoryLog;
use Carbon\Carbon;
use App\Http\Controllers\Admin\HistorylogController;

class OcrInventoryController extends Controller
{
    /**
     * Handles the receipt upload, trying Gemini first and falling back to Mistral.
     */
    public function uploadReceipt(Request $request)
    {
        try {
            // 1. Validate the uploaded image
            $request->validate([
                'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $file = $request->file('receipt_image');
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType = $file->getMimeType();

            // 2. Define a universal prompt and JSON schema
            $prompt = $this->getOcrPrompt();
            $jsonSchema = $this->getJsonSchema();
            $extractedJson = null;

            // 3. Try Gemini first
            try {
                Log::info('Attempting OCR with Gemini AI...');
                $extractedJson = $this->callGeminiApi($prompt, $mimeType, $imageData, $jsonSchema);
                Log::info('Successfully processed OCR with Gemini AI.');
            } catch (Exception $geminiException) {
                Log::warning('Gemini AI failed. Falling back to Mistral AI.', [
                    'error' => $geminiException->getMessage()
                ]);

                // 4. If Gemini fails, try Mistral as a fallback
                try {
                    Log::info('Attempting OCR with Mistral AI...');
                    $extractedJson = $this->callMistralApi($prompt, $mimeType, $imageData);
                    Log::info('Successfully processed OCR with fallback Mistral AI.');
                } catch (Exception $mistralException) {
                    Log::error('Fallback Mistral AI also failed.', [
                        'error' => $mistralException->getMessage()
                    ]);
                    // If both fail, throw a combined error message
                    throw new Exception("The primary AI service failed (Error: {$geminiException->getMessage()}) and the fallback service also failed (Error: {$mistralException->getMessage()}).");
                }
            }

            // 5. Process the successful JSON response
            OcrInventoryLog::create(['raw_text' => $extractedJson]);
            $extractedData = json_decode($extractedJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON received from AI: " . json_last_error_msg());
            }
            if (empty($extractedData['data'])) {
                throw new Exception("The AI successfully read the receipt but found no product data.");
            }

            // 6. Normalize and return the data
            $products = array_map(function($item) {
                 $productName = $this->cleanProductName($item['product_name'] ?? '');
                 $brandName = isset($item['brand_name']) && trim($item['brand_name']) !== '' ? trim($item['brand_name']) : null;
                 $form = $this->normalizeForm($item['form'] ?? 'N/A');
                 $seasonPeak = $this->getSeasonPeakForProduct($productName);
                 $strength = (!empty($item['strength']) && $item['strength'] !== 'N/A') ? $item['strength'] : $this->extractStrength($productName);
                 return [
                     'quantity' => (int)($item['quantity'] ?? 0),
                     'brand_name' => $brandName,
                     'product_name' => $productName,
                     'form' => $form,
                     'batch_number' => $item['batch_number'] ?? '',
                     'expiry_date' => $this->normalizeDate($item['expiry_date'] ?? ''),
                     'strength' => $strength,
                     'season_peak' => $seasonPeak
                 ];
            }, $extractedData['data']);

            return response()->json([
                'status' => 'success',
                'message' => '✅ Data extracted successfully! Please review and save.',
                'data' => $products
            ]);

        } catch (Exception $e) {
            Log::error("OCR Processing Error", ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calls the Gemini API to perform OCR.
     * @throws Exception if the API call or processing fails.
     */
    private function callGeminiApi(string $prompt, string $mimeType, string $imageData, array $jsonSchema): string
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            throw new Exception('Gemini API Key is not set.');
        }

        $response = Http::timeout(120)->post(
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

        if ($response->failed()) {
            throw new Exception("Gemini API request failed. Status: " . $response->status());
        }

        $result = $response->json();
        $extractedJson = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (empty($extractedJson)) {
            $reason = $result['candidates'][0]['finishReason'] ?? 'unknown reason';
            throw new Exception("Gemini processing failed. Reason: {$reason}");
        }

        return $extractedJson;
    }

    /**
     * Calls the Mistral API as a fallback for OCR.
     * @throws Exception if the API call or processing fails.
     */
    private function callMistralApi(string $prompt, string $mimeType, string $imageData): string
    {
        $apiKey = env('MISTRAL_API_KEY');
        if (!$apiKey) {
            throw new Exception('Mistral API Key is not set.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(120)
            ->post('https://api.mistral.ai/v1/chat/completions', [
                'model' => 'mistral-large-latest',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:$mimeType;base64,$imageData"
                                ]
                            ]
                        ]
                    ]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

        if ($response->failed()) {
            throw new Exception("Mistral API request failed. Status: " . $response->status());
        }

        $result = $response->json();
        $extractedJson = $result['choices'][0]['message']['content'] ?? null;

        if (empty($extractedJson)) {
            $reason = $result['choices'][0]['finish_reason'] ?? 'unknown reason';
            throw new Exception("Mistral processing failed. Reason: {$reason}");
        }

        return $extractedJson;
    }

    /**
     * Returns the detailed prompt for the AI.
     */
    private function getOcrPrompt(): string
    {
        return "You are a highly intelligent and detail-oriented OCR system specializing in pharmaceutical receipts. Your primary goal is to analyze the provided 'Vitalis Pharma Phil. Corp. Acknowledgement Receipt' and extract ALL product data from the table with exceptional accuracy, interpreting and correcting text where appropriate to provide clean, standardized data. RULES FOR EXTRACTION AND INTELLIGENT INTERPRETATION: 1. **Quantity**: Extract the exact numeric quantity. 2. **Brand Name**: Extract the *exact text* from the 'Brand Name' column. If the cell is BLANK on the receipt, return an empty string. DO NOT GUESS or infer from the generic name. 3. **Generic Name**: Extract the generic name. If there are any misspellings or abbreviations, provide the most common and correct spelling. CRITICALLY, DO NOT include the strength (e.g., '500mg') in this field. 4. **Strength**: Extract the precise strength, dosage, or concentration (e.g., '500mg', '10mg/5mL', '10%'). This is often written next to the generic name. If it is not available, you MUST provide 'N/A'. 5. **Form**: Extract the product's form. This is critical. If there are misspellings (e.g., 'injction'), abbreviations (e.g., 'tab'), or unclear handwriting, you MUST interpret and provide the **standard, singular pharmaceutical form** (e.g., 'Tablet', 'Capsule', 'Syrup', 'Injection'). If unidentifiable, respond with 'N/A'. 6. **Batch Number**: Extract the alphanumeric batch number *exactly* as written. 7. **Expiry Date**: Extract the expiry date and convert it to a strict YYYY-MM-DD format. 8. **Output**: Return ONLY the JSON object conforming strictly to the specified schema. Do not add any introductory or concluding text. Ensure all required fields are present and accurately populated based on these rules.";
    }

    /**
     * Returns the required JSON schema for the AI response.
     */
    private function getJsonSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'properties' => [
                'data' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'quantity' => ['type' => 'NUMBER', 'description' => 'The numeric quantity.'],
                            'brand_name' => ['type' => 'STRING', 'description' => 'The exact brand name. Empty if blank.'],
                            'product_name' => ['type' => 'STRING', 'description' => 'The generic name, corrected and standardized, without strength.'],
                            'strength' => ['type' => 'STRING', 'description' => 'The specific strength (e.g., "500mg"). "N/A" if not present.'],
                            'form' => ['type' => 'STRING', 'description' => 'The standard pharmaceutical form (e.g., Tablet, Syrup).'],
                            'batch_number' => ['type' => 'STRING', 'description' => 'The alphanumeric batch number.'],
                            'expiry_date' => ['type' => 'STRING', 'description' => 'The expiry date in YYYY-MM-DD format.'],
                        ],
                        'required' => ['quantity', 'product_name', 'strength', 'form', 'batch_number', 'expiry_date', 'brand_name']
                    ]
                ]
            ]
        ];
    }

    /**
     * Saves the validated and confirmed inventory data to the database.
     */
    public function saveInventory(Request $request)
    {
        $products = $request->input('products', []);

        if (empty($products)) {
            return response()->json(['status' => 'error', 'message' => 'No product data received.'], 400);
        }

        $results = [
            'inventory_created' => [],
            'duplicates' => [],
            'validation_errors' => [],
            'errors' => []
        ];

        $hasValidationErrors = false;

        foreach ($products as $index => $data) {
            try {
                $rules = [
                    'product_name' => 'required|string|max:255',
                    'brand_name' => 'nullable|string|max:255',
                    'form' => 'required|string|max:50',
                    'strength' => 'required|string|max:50',
                    'season_peak' => 'required|string|in:Tag-init,Tag-ulan,All-Year',
                    'batch_number' => 'required|string|max:50',
                    'expiry_date' => 'required|date|after_or_equal:today',
                    'quantity' => 'required|integer|min:1',
                    'location' => 'required|string|max:255',
                ];

                $messages = [
                    'expiry_date.after_or_equal' => 'The expiry date is outdated. Please use a future date.'
                ];

                $validator = Validator::make($data, $rules, $messages);

                if ($validator->fails()) {
                    $results['validation_errors'][$index] = $validator->errors();
                    $hasValidationErrors = true;
                    continue;
                }

                $productName = $data['product_name'];
                $strength = $data['strength'];
                $form = $data['form'];

                $product = Product::firstOrCreate(
                    ['generic_name' => $productName, 'strength' => $strength, 'form' => $form],
                    ['brand_name' => $data['brand_name'] ?? null, 'season_peak' => $data['season_peak'], 'is_auto_created' => true]
                );

                $location = Location::firstOrCreate(['province' => $data['location']]);

                $inventory = Inventory::where('product_id', $product->id)
                    ->where('batch_number', $data['batch_number'])
                    ->where('location_id', $location->id)
                    ->first();

                if ($inventory) {
                    $results['duplicates'][] = "{$product->generic_name} (Batch: {$data['batch_number']}) at {$location->province}";
                    continue;
                }

                Inventory::create([
                    'product_id' => $product->id,
                    'batch_number' => $data['batch_number'],
                    'expiry_date' => $data['expiry_date'],
                    'quantity' => $data['quantity'],
                    'location_id' => $location->id
                ]);

                HistorylogController::addstocklog(
                    'add',
                    "Added new stock via OCR: {$data['quantity']} unit(s) of {$product->generic_name} {$product->strength} (Batch: {$data['batch_number']}) was added to {$location->province}."
                );

                $results['inventory_created'][] = "{$product->generic_name} (Batch: {$data['batch_number']}) at {$location->province}";

            } catch (Exception $e) {
                $productNameForError = $data['product_name'] ?? 'Unknown';
                $results['errors'][] = ['product' => $productNameForError, 'error' => $e->getMessage()];
                Log::error("Failed to save product inventory: " . $e->getMessage(), ['product_data' => $data]);
            }
        }

        if ($hasValidationErrors) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Please review the errors below and correct the data.',
                'errors' => $results['validation_errors']
            ], 422);
        }
        
        $response = [
            'status' => (empty($results['errors']) && empty($results['duplicates'])) ? 'success' : 'partial',
            'message' => 'Inventory processing complete.',
            'results' => $results
        ];

        return response()->json($response);
    }
    
    /**
     * Retrieves a list of available locations.
     */
    public function getLocations()
    {
        $locations = Location::orderBy('province')->pluck('province')->toArray();
        if (empty($locations)) {
            $locations = ['Tarlac', 'Pampanga', 'Pangasinan', 'Manila', 'Baguio']; // Fallback
        }
        return response()->json(['locations' => $locations]);
    }

    /**
     * Determines the season peak for a given product using an AI call.
     */
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

    /**
     * Normalizes a given pharmaceutical form string to a standard term.
     */
    private function normalizeForm(string $form): string
    {
        // This function is very long, so it's omitted here for brevity.
        // Assume the full function from the previous context is present.
        $form = strtolower(trim($form));
        $map = [
            'Tablets' => ['tablet', 'tablets', 'pill', 'pills', 'cap', 'caps', 'tab', 'tabs', 'table', 'tblt', 'tbl', 'tabl', 'tb', 'tblet', 'tablt', 'tabblet', 'tabllet', 'tabet', 'tablt', 'tblt', 'tblet', 'tabs', 'tabs.', 'tab.', 'capsule', 'capsules', 'cpsl', 'cap.', 'caps.', 'cp', 'cps', 'kaps', 'kapsul', 'kapsule', 'kapsules', 'pil', 'pils', 'píl', 'píls'],
            'Injectables' => ['inj', 'inject', 'injection', 'injections', 'injectable', 'injectables', 'inject', 'iv', 'vial', 'vials', 'ampule', 'ampules', 'ampoule', 'ampoules', 'injec', 'injction', 'im', 'sc', 'subcut', 'intramuscular', 'injek', 'injekts', 'injektable', 'injektion', 'injekshun', 'injek.', 'injeks', 'injekcija', 'injekcije', 'iv.', 'i.v.', 'intraven', 'intraven.', 'intrav.', 'intram.', 'intramus.', 'intramusc.', 'subq', 'sub-q', 'subcutan', 'subcutane', 'subkutan', 'subkut.', 'subkutane', 'ampul', 'ampul.', 'ampoule', 'ampoules', 'ampul.', 'ampulla', 'ampullae', 'vial.', 'vials.', 'viall', 'vialle', 'vialls', 'viall.', 'vialle.', 'vialls.'],
            'Syrup' => ['syrup', 'syrups', 'suspension', 'suspensions', 'liquid', 'liquids', 'syr', 'syrupn', 'elixir', 'elixirs', 'mixture', 'mixtures', 'liq', 'lqd', 'sirup', 'sirups', 'sir.', 'syrp', 'syrps', 'syrp.', 'syrps.', 'sirup.', 'sirups.', 'susp', 'susp.', 'susps', 'susps.', 'suspens', 'suspens.', 'suspense', 'suspense.', 'suspn', 'suspn.', 'suspns', 'suspns.', 'liq.', 'lqd.', 'liquid.', 'liquids.', 'liqs', 'liqs.', 'lq', 'lq.', 'lqs', 'lqs.', 'elix.', 'elixs', 'elixs.', 'elixir.', 'elixirs.', 'elx', 'elx.', 'elxs', 'elxs.', 'mixt', 'mixt.', 'mixts', 'mixts.', 'mixtur', 'mixtur.', 'mixturs', 'mixturs.'],
            'Ointment' => ['ointment', 'ointments', 'cream', 'creams', 'gel', 'gels', 'oint', 'topical', 'top', 'creme', 'cremes', 'ung', 'salve', 'balm', 'oint.', 'ointments.', 'ointmnt', 'ointmnt.', 'ointmnts', 'ointmnts.', 'ointmen', 'ointmen.', 'ointmens', 'ointmens.', 'ointm.', 'ointm.', 'ointms', 'ointms.', 'cream.', 'creams.', 'crem', 'crem.', 'crems', 'crems.', 'creme.', 'cremes.', 'crème', 'crèmes', 'crème.', 'crèmes.', 'gel.', 'gels.', 'jel', 'jel.', 'jels', 'jels.', 'topical.', 'topicals', 'topicals.', 'top.', 'tops', 'tops.', 'topik', 'topik.', 'topiks', 'topiks.', 'ung.', 'unguent', 'unguent.', 'unguents', 'unguents.', 'salve.', 'salves', 'salves.', 'balm.', 'balms', 'balms.'],
            'Drops' => ['drops', 'gtts', 'droplets', 'eye drops', 'ear drops', 'nasal drops', 'opthalmic drops', 'otic drops', 'gt', 'drop', 'drps', 'drps.', 'drp', 'drp.', 'gtt', 'gtt.', 'gtts.', 'gttss', 'gttss.', 'eyedrops', 'eyedrop', 'eyedrops.', 'eyedrop.', 'eardrops', 'eardrop', 'eardrops.', 'eardrop.', 'nasaldrops', 'nasaldrop', 'nasaldrops.', 'nasaldrop.', 'opthalmicdrops', 'opthalmicdrop', 'opthalmicdrops.', 'opthalmicdrop.', 'oticdrops', 'oticdrop', 'oticdrops.', 'oticdrop.', 'ophthalmic drops', 'ophthalmic drop', 'ophthalmicdrops', 'ophthalmicdrop', 'ophthalmicdrops.', 'ophthalmicdrop.'],
            'Solution' => ['solution', 'solutions', 'soln', 'sol', 'solutn', 'soltn', 'solns', 'solut', 'solvent', 'sol.', 'sols', 'sols.', 'soln.', 'solns.', 'solut.', 'soluts', 'soluts.', 'solutn.', 'solutns', 'solutns.', 'soltn.', 'soltns', 'soltns.', 'solv', 'solv.', 'solvs', 'solvs.', 'solvent.', 'solvents', 'solvents.', 'solutio', 'solutio.', 'solutiones', 'solutiones.', 'soluc', 'soluc.', 'solucion', 'solucion.', 'solucions', 'solucions.'],
            'Powder' => ['powder', 'powders', 'pwd', 'pdr', 'granules', 'granule', 'grs', 'grnls', 'powd', 'powd.', 'powds', 'powds.', 'pdr.', 'pdrs', 'pdrs.', 'pwd.', 'pwds', 'pwds.', 'gran.', 'gran.', 'grans', 'grans.', 'granul', 'granul.', 'granuls', 'granuls.', 'grs.', 'grnls.', 'grn', 'grn.', 'grns', 'grns.', 'poudre', 'poudres', 'poudre.', 'poudres.', 'pulv', 'pulv.', 'pulvs', 'pulvs.', 'pulver', 'pulver.', 'pulvers', 'pulvers.'],
            'Aerosol' => ['aerosol', 'aerosols', 'inhaler', 'inhalers', 'puffer', 'puffers', 'mdpi', 'dpi', 'nebulizer', 'nebulizers', 'aero.', 'aeros.', 'aerosol.', 'aerosols.', 'aerosoles', 'aerosoles.', 'inhal.', 'inhal.', 'inhals', 'inhals.', 'inh.', 'inh.', 'inhs', 'inhs.', 'puff.', 'puffs', 'puffs.', 'pfr', 'pfr.', 'pfrs', 'pfrs.', 'mdpi.', 'dpi.', 'neb', 'neb.', 'nebs', 'nebs.', 'nebul.', 'nebul.', 'nebuls', 'nebuls.', 'nebuliz', 'nebuliz.', 'nebulizer.', 'nebulizers.', 'nebuliz.', 'nebulizs', 'nebulizs.'],
            'Suppository' => ['suppository', 'suppositories', 'supp', 'suppos', 'rectal', 'vaginal', 'pessary', 'pessaries', 'suppos.', 'suppos.', 'supps', 'supps.', 'suppositor', 'suppositor.', 'suppositors', 'suppositors.', 'rect.', 'rect.', 'rects', 'rects.', 'vag.', 'vag.', 'vags', 'vags.', 'pess.', 'pess.', 'pesses', 'pesses.', 'pessar', 'pessar.', 'pessars', 'pessars.', 'suppositorium', 'suppositoria', 'suppositorium.', 'suppositoria.'],
            'Patch' => ['patch', 'patches', 'transdermal', 'tds', 'td', 'patch.', 'patches.', 'patchs', 'patchs.', 'ptch', 'ptch.', 'ptchs', 'ptchs.', 'transderm.', 'transderm.', 'transderms', 'transderms.', 'td.', 'tds.', 'transdermal.', 'transdermals', 'transdermals.', 'plaster', 'plasters', 'plaster.', 'plasters.', 'pflaster', 'pflasters', 'pflaster.', 'pflasters.'],
            'Spray' => ['spray', 'sprays', 'mist', 'mists', 'nasal spray', 'oral spray', 'spray.', 'sprays.', 'spr', 'spr.', 'sprs', 'sprs.', 'mist.', 'mists.', 'mst', 'mst.', 'msts', 'msts.', 'nasalspray', 'nasalspray.', 'oralspray', 'oralspray.', 'nasspray', 'nasspray.', 'mundspray', 'mundspray.', 'sprayflasche', 'sprayflaschen', 'sprayflasche.', 'sprayflaschen.'],
            'Oral' => ['o', 'or', 'orl', 'oral', 'po', 'by mouth', 'p.o.', 'per os', 'oral.', 'orals', 'orals.', 'or.', 'orl.', 'po.', 'p.o', 'p.o..', 'peros', 'peros.', 'peroral', 'peroral.', 'perorals', 'perorals.', 'mouth', 'mouth.', 'mouths', 'mouths.', 'oralis', 'oralis.', 'orale', 'orale.', 'orales', 'orales.'],
            'Film' => ['film', 'films', 'oral film', 'dissolving film', 'strip', 'strips', 'film.', 'films.', 'flm', 'flm.', 'flms', 'flms.', 'oralfilm', 'oralfilm.', 'dissolvingfilm', 'dissolvingfilm.', 'strip.', 'strips.', 'stripp', 'stripp.', 'stripps', 'stripps.', 'folie', 'folien', 'folie.', 'folien.', 'filmbasierte', 'filmbasierte.', 'filmbasierter', 'filmbasierter.'],
            'Lozenge' => ['lozenge', 'lozenges', 'troche', 'troches', 'pastille', 'pastilles', 'lozenge.', 'lozenges.', 'loz', 'loz.', 'lozs', 'lozs.', 'troche.', 'troches.', 'trch', 'trch.', 'trchs', 'trchs.', 'pastille.', 'pastilles.', 'past.', 'past.', 'pasts', 'pasts.', 'pastill', 'pastill.', 'pastills', 'pastills.', 'lutschtablette', 'lutschtabletten', 'lutscher', 'lutschers'],
            'Gum' => ['gum', 'gums', 'chewing gum', 'medicated gum', 'gum.', 'gums.', 'gummi', 'gummis', 'gummi.', 'gummis.', 'chewinggum', 'chewinggum.', 'medicatedgum', 'medicatedgum.', 'kaugummi', 'kaugummis', 'kaugummi.', 'kaugummis.', 'kaufgummi', 'kaufgummis', 'kaufgummi.', 'kaufgummis.'],
            'Implant' => ['implant', 'implants', 'insert', 'inserts', 'pellet', 'pellets', 'implant.', 'implants.', 'impl', 'impl.', 'impls', 'impls.', 'insert.', 'inserts.', 'ins', 'ins.', 'inss', 'inss.', 'pellet.', 'pellets.', 'pell', 'pell.', 'pells', 'pells.', 'pel', 'pel.', 'pels', 'pels.', 'implanon', 'implanons', 'implanon.', 'implanons.'],
            'Enema' => ['enema', 'enemas', 'rectal enema', 'fleetenema', 'enema.', 'enemas.', 'enem', 'enem.', 'enems', 'enems.', 'rectalenema', 'rectalenema.', 'fleetenema.', 'klistier', 'klistiere', 'klistier.', 'klistiere.', 'einlauf', 'einläufe', 'einlauf.', 'einläufe.'],
            'Lotion' => ['lotion', 'lotions', 'liniment', 'liniments', 'lotion.', 'lotions.', 'lot', 'lot.', 'lots', 'lots.', 'liniment.', 'liniments.', 'linim', 'linim.', 'linims', 'linims.', 'loción', 'lociones', 'loción.', 'lociones.', 'lotionen', 'lotionen.'],
            'Shampoo' => ['shampoo', 'shampoos', 'medicated shampoo', 'shampoo.', 'shampoos.', 'shamp', 'shamp.', 'shamps', 'shamps.', 'medicatedshampoo', 'medicatedshampoo.', 'shampoing', 'shampoings', 'shampoing.', 'shampoings.', 'champú', 'champús', 'champú.', 'champús.'],
            'Foam' => ['foam', 'foams', 'medicated foam', 'foam.', 'foams.', 'fom', 'fom.', 'foms', 'foms.', 'medicatedfoam', 'medicatedfoam.', 'schaum', 'schäume', 'schaum.', 'schäume.', 'mousse', 'mousses', 'mousse.', 'mousses.'],
            'Paste' => ['paste', 'pastes', 'dental paste', 'paste.', 'pastes.', 'pst', 'pst.', 'psts', 'psts.', 'dentalpaste', 'dentalpaste.', 'pasta', 'pastae', 'pasta.', 'pastae.', 'zahnpasta', 'zahnpasten', 'zahnpasta.', 'zahnpasten.'],
            'Gas' => ['gas', 'gasses', 'inhalation gas', 'medical gas', 'gas.', 'gasses.', 'gs', 'gs.', 'gss', 'gss.', 'inhalationgas', 'inhalationgas.', 'medicalgas', 'medicalgas.', 'gáz', 'gázok', 'gáz.', 'gázok.', 'gasinhalat', 'gasinhalate', 'gasinhalat.', 'gasinhalate.'],
            'Disk' => ['disk', 'disks', 'disc', 'discs', 'diskette', 'diskettes', 'disk.', 'disks.', 'dsk', 'dsk.', 'dsks', 'dsks.', 'disc.', 'discs.', 'dsc', 'dsc.', 'dscs', 'dscs.', 'diskette.', 'diskettes.', 'disket', 'disket.', 'diskets', 'diskets.', 'scheibe', 'scheiben', 'scheibe.', 'scheiben.'],
            'Wafer' => ['wafer', 'wafers', 'oral wafer', 'wafer.', 'wafers.', 'wfr', 'wfr.', 'wfrs', 'wfrs.', 'oralwafer', 'oralwafer.', 'oblea', 'obleas', 'oblea.', 'obleas.', 'hostie', 'hosties', 'hostie.', 'hosties.'],
            'Powder for Injection' => ['powder for injection', 'lyophilized', 'lyo', 'lyophilizate', 'powderforinjection', 'powderforinjection.', 'lyophilized.', 'lyophilizate.', 'lyo.', 'lyophil.', 'lyophil.', 'lyophilisat', 'lyophilisate', 'lyophilisat.', 'lyophilisate.', 'pulver zur injektion', 'pulver zur injektion.', 'poudre pour injection', 'poudre pour injection.'],
            'Emulsion' => ['emulsion', 'emulsions', 'emul', 'emulsn', 'emulsion.', 'emulsions.', 'emuls.', 'emulsn.', 'emulsns', 'emulsns.', 'emulsio', 'emulsio.', 'emulsionen', 'emulsionen.', 'emulgat', 'emulgate', 'emulgat.', 'emulgate.', 'emulgator', 'emulgatoren', 'emulgator.', 'emulgatoren.'],
            'Tincture' => ['tincture', 'tinctures', 'tinc', 'tinct', 'tincture.', 'tinctures.', 'tinct.', 'tincts', 'tincts.', 'tinktur', 'tinkturen', 'tinktur.', 'tinkturen.', 'tintura', 'tinturae', 'tintura.', 'tinturae.', 'tint.', 'tint.', 'tints', 'tints.'],
            'Sachet' => ['sachet', 'sachets', 'sach', 'sach.', 'sachs', 'sachs.', 'beutel', 'beuteln', 'beutel.', 'beuteln.', 'pouch', 'pouches', 'pouch.', 'pouches.', 'sobe', 'sobes', 'sobe.', 'sobes.'],
            'Granules' => ['granules', 'granule', 'gran.', 'gran.', 'grans', 'grans.', 'granul', 'granul.', 'granuls', 'granuls.', 'granulat', 'granulate', 'granulat.', 'granulate.', 'granulés', 'granulé', 'granulés.', 'granulé.']
        ];

        foreach ($map as $standard => $variations) {
            if (in_array($form, $variations)) {
                return $standard;
            }
        }

        return 'N/A';
    }

    /**
     * Parses and normalizes a date string into YYYY-MM-DD format.
     */
    private function normalizeDate(string $date): string
    {
        if (empty(trim($date))) { return '1970-01-01'; } // Return an obviously invalid past date
        try {
            $carbonDate = Carbon::parse($date);
            // Handles 2-digit years
            if ($carbonDate->year < 100) {
                // Heuristic: If the 2-digit year is within the next 15 years, it's 20xx. Otherwise, it's 19xx.
                $carbonDate->year += ($carbonDate->year <= (int)Carbon::now()->format('y') + 15) ? 2000 : 1900;
            }
            return $carbonDate->format('Y-m-d');
        } catch (Exception $e) {
            Log::warning("Could not parse date format: {$date}. Falling back. Error: " . $e->getMessage());
            return '2025-01-01'; // Fallback
        }
    }
    
    /**
     * Cleans up a product name string.
     */
    private function cleanProductName(string $name): string
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    /**
     * Extracts the strength/dosage from a product name string.
     */
    private function extractStrength(string $productName): string
    {
        $pattern = '/(\d+(\.\d+)?\s*(mg|mcg|g|ml|%|iu|units|mg\/ml|mcg\/ml)(\s*\/\s*\d*\.?\d*\s*(ml|l))?)/i';
        if (preg_match($pattern, $productName, $matches)) {
            return trim($matches[0]);
        }
        return 'N/A';
    }
}