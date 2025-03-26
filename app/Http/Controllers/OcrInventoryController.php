<?php
    namespace App\Http\Controllers;
    
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Log;
    use App\Models\Inventory;
    use App\Models\Product;
    use App\Models\Location;
    use App\Models\OcrInventoryLog;
    
    class OcrInventoryController extends Controller
    {
        public function uploadReceipt(Request $request)
        {
            try {
                // ✅ Validate uploaded image
                $request->validate([
                    'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
                ]);
        
                // ✅ Move uploaded image to /public/uploads/
                $file = $request->file('receipt_image');
                $fileName = time() . '_' . $file->getClientOriginalName(); // Unique name
                $file->move(public_path('uploads'), $fileName);
        
                // ✅ Get public URL of uploaded image
                $imageUrl = asset("uploads/{$fileName}");
        
                // ✅ Convert image to Base64 for Google Vision API
                $imageData = base64_encode(file_get_contents(public_path("uploads/{$fileName}")));
                
                $apiKey = env('GOOGLE_VISION_API_KEY');
        
                if (!$apiKey) {
                    return response()->json(['message' => 'Error: Google API Key is missing.'], 400);
                }
        
                // ✅ Send image to Google Vision API
                $visionApiUrl = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";
                $payload = [
                    "requests" => [
                        [
                            "image" => ["content" => $imageData],
                            "features" => [["type" => "TEXT_DETECTION"]]
                        ]
                    ]
                ];
        
                $response = Http::post($visionApiUrl, $payload);
                $visionResult = $response->json();
        
                if (!isset($visionResult['responses'][0]['fullTextAnnotation']['text'])) {
                    return response()->json(['message' => 'Error: No text detected.'], 400);
                }
        
                // ✅ Extract text
                $extractedText = $visionResult['responses'][0]['fullTextAnnotation']['text'];
                Log::info("OCR Extracted Text: \n" . $extractedText);
        
                // ✅ Save raw OCR data
                OcrInventoryLog::create(['raw_text' => $extractedText]);
        
                // ✅ Filtering relevant data only (Using Regex)
                preg_match_all('/(\d+)\s+([A-Za-z\s]+)\s+([A-Za-z\s]+)\s+(Oral|Injectable|Tablet)\s+(\d+)\s+([A-Za-z]+\s\d{1,2},\s\d{4})/', 
                    $extractedText, $matches, PREG_SET_ORDER);
        
                $products = [];
                foreach ($matches as $match) {
                    $formattedDate = date('Y-m-d', strtotime(trim($match[6]))); // Convert to YYYY-MM-DD format
        
                    $products[] = [
                        'quantity'     => (int) trim($match[1]),  // Quantity
                        'brand_name'   => trim($match[2]),        // Brand Name
                        'product_name' => trim($match[3]),        // Generic Name
                        'form'         => trim($match[4]),        // Form
                        'batch_number' => trim($match[5]),        // Batch Number
                        'expiry_date'  => $formattedDate,         // Expiry Date
                    ];
                }
        
                if (empty($products)) {
                    return response()->json(['message' => '❌ Error: No valid product data detected.'], 400);
                }
        
                return response()->json(['message' => '✅ Data extracted successfully!', 'data' => $products], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => '❌ Server error: ' . $e->getMessage(),
                    'line'    => $e->getLine(),
                    'file'    => $e->getFile()
                ], 500);
            }
        }
        
        
    
        public function saveInventory(Request $request)
        {
            try {
                $request->validate([
                    'products' => 'required|array',
                    'products.*.product_name' => 'required|string',
                    'products.*.batch_number' => 'required|string',
                    'products.*.expiry_date'  => 'required|date',
                    'products.*.quantity'     => 'required|integer|min:1',
                    'products.*.brand_name'   => 'nullable|string',
                    'products.*.form'         => 'nullable|string',
                ]);
    
                $missingProducts = [];
    
                foreach ($request->products as $data) {
                    $product = Product::where('generic_name', $data['product_name'])->first();
    
                    if (!$product) {
                        $missingProducts[] = $data['product_name'];
                        continue;
                    }
    
                    Inventory::create([
                        'product_id'   => $product->id,
                        'batch_number' => $data['batch_number'],
                        'expiry_date'  => $data['expiry_date'],
                        'quantity'     => $data['quantity'],
                        'location_id'  => $data['location'] ? Location::firstOrCreate(['province' => $data['location']])->id : null
                    ]);
                }
                if (!empty($missingProducts)) {
                    return response()->json([
                        'status' => 'warning',
                        'message' => 'Some products are not registered. Please add them first.',
                        'missing_products' => $missingProducts
                    ], 400);
                }
        
                return response()->json([
                    'status' => 'success',
                    'message' => '✅ Inventory successfully added!'
                ], 200);
        
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => '❌ Server error: ' . $e->getMessage(),
                    'line'    => $e->getLine(),
                    'file'    => $e->getFile()
                ], 500);
            }
        }
    }        