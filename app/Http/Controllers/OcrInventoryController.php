<?php
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Log;
// use App\Models\Inventory;
// use App\Models\Product;
// use App\Models\Location;
// use App\Models\OcrInventoryLog;

// class OcrInventoryController extends Controller
// {
//     public function uploadReceipt(Request $request)
//     {
//         try {
//             // ✅ Validate uploaded image
//             $request->validate([
//                 'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
//             ]);

//             // ✅ Store uploaded image in `storage/app/public/receipts/`
//             $path = $request->file('receipt_image')->store('receipts', 'public');
//             $fullPath = public_path("storage/{$path}"); // Ensure correct path

//             // ✅ Debugging
//             Log::info("Uploaded file stored at: " . $fullPath);
//             if (!file_exists($fullPath)) {
//                 return response()->json(['message' => '❌ Error: File not found in storage.'], 400);
//             }

//             // ✅ Read image as Base64 (required by Google Vision API)
//             $imageData = base64_encode(file_get_contents($fullPath));

//             // ✅ Use Google Vision API Key from .env
//             $apiKey = env('GOOGLE_VISION_API_KEY');
//             if (!$apiKey) {
//                 return response()->json(['message' => '❌ Error: Google API Key is missing.'], 400);
//             }

//             // ✅ Google Cloud Vision API Endpoint
//             $visionApiUrl = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";

//             // ✅ Prepare the request payload
//             $payload = [
//                 "requests" => [
//                     [
//                         "image" => ["content" => $imageData],
//                         "features" => [["type" => "TEXT_DETECTION"]]
//                     ]
//                 ]
//             ];

//             // ✅ Make the API request using Laravel HTTP client
//             $response = Http::post($visionApiUrl, $payload);
//             $visionResult = $response->json();

//             // ✅ Debug API response
//             Log::info("Google Vision API Response:", $visionResult);

//             if (!isset($visionResult['responses'][0]['fullTextAnnotation']['text'])) {
//                 return response()->json(['message' => '❌ Error: No text detected.'], 400);
//             }

//             // ✅ Extract OCR text
//             $extractedText = $visionResult['responses'][0]['fullTextAnnotation']['text'];

//             // ✅ Save raw OCR text for debugging
//             OcrInventoryLog::create(['raw_text' => $extractedText]);

//             // ✅ Debug extracted text
//             Log::info("OCR Extracted Text: \n" . $extractedText);

//             // ✅ Extract structured data using regex
//             preg_match('/Product:\s*(.+)/', $extractedText, $productMatch);
//             preg_match('/Batch:\s*(\w+)/', $extractedText, $batchMatch);
//             preg_match('/Expiry:\s*(\d{4}-\d{2}-\d{2})/', $extractedText, $expiryMatch);
//             preg_match('/Quantity:\s*(\d+)/', $extractedText, $quantityMatch);
//             preg_match('/Location:\s*(.+)/', $extractedText, $locationMatch);

//             if (!$productMatch || !$batchMatch || !$expiryMatch || !$quantityMatch || !$locationMatch) {
//                 return response()->json(['message' => '❌ Error: Could not detect required fields.'], 400);
//             }

//             // ✅ Extract values
//             $productName = trim($productMatch[1]);
//             $batchNumber = trim($batchMatch[1]);
//             $expiryDate = trim($expiryMatch[1]);
//             $quantity = (int)trim($quantityMatch[1]);
//             $locationName = trim($locationMatch[1]);

//             // ✅ Find or create product
//             $product = Product::firstOrCreate(['generic_name' => $productName]);

//             // ✅ Find location
//             $location = Location::where('province', $locationName)->first();
//             if (!$location) {
//                 return response()->json(['message' => '❌ Error: Location not found.'], 400);
//             }

//             // ✅ Add inventory
//             $inventory = Inventory::create([
//                 'location_id' => $location->id,
//                 'product_id'  => $product->id,
//                 'batch_number' => $batchNumber,
//                 'expiry_date' => $expiryDate,
//                 'quantity' => $quantity,
//             ]);

//             // ✅ Update OCR log with structured data
//             OcrInventoryLog::where('raw_text', $extractedText)->update([
//                 'product_name' => $productName,
//                 'batch_number' => $batchNumber,
//                 'expiry_date' => $expiryDate,
//                 'quantity' => $quantity,
//                 'location' => $locationName,
//             ]);

//             return response()->json(['message' => '✅ Inventory successfully added!', 'inventory' => $inventory], 200);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'message' => '❌ Server error: ' . $e->getMessage(),
//                 'line'    => $e->getLine(),
//                 'file'    => $e->getFile()
//             ], 500);
//         }
//     }
// }


// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Log;
// use App\Models\Inventory;
// use App\Models\Product;
// use App\Models\Location;
// use App\Models\OcrInventoryLog;

// class OcrInventoryController extends Controller
// {
//     public function uploadReceipt(Request $request)
//     {
//         // dd($request->toArray());
//         try {
//             // ✅ Validate uploaded image
//             $request->validate([
//                 'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
//             ]);

//             // ✅ Store uploaded image in `storage/app/public/receipts/`
//             $path = $request->file('receipt_image')->store('receipts', 'public');
//             $fullPath = public_path("storage/{$path}");

//             // ✅ Debugging: Ensure file exists
//             if (!file_exists($fullPath)) {
//                 return response()->json(['message' => '❌ Error: File not found in storage.'], 400);
//             }

//             // ✅ Read image as Base64 (required by Google Vision API)
//             $imageData = base64_encode(file_get_contents($fullPath));

//             // ✅ Google Vision API Key from .env
//             $apiKey = env('GOOGLE_VISION_API_KEY');
//             if (!$apiKey) {
//                 return response()->json(['message' => '❌ Error: Google API Key is missing.'], 400);
//             }

//             // ✅ Google Cloud Vision API Endpoint
//             $visionApiUrl = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";

//             // ✅ Prepare the request payload
//             $payload = [
//                 "requests" => [
//                     [
//                         "image" => ["content" => $imageData],
//                         "features" => [["type" => "TEXT_DETECTION"]]
//                     ]
//                 ]
//             ];

//             // ✅ Make the API request using Laravel HTTP client
//             $response = Http::post($visionApiUrl, $payload);
//             $visionResult = $response->json();

//             // ✅ Debug API response
//             Log::info("Google Vision API Response:", $visionResult);

//             if (!isset($visionResult['responses'][0]['fullTextAnnotation']['text'])) {
//                 return response()->json(['message' => '❌ Error: No text detected.'], 400);
//             }

//             // ✅ Extract OCR text
//             $extractedText = $visionResult['responses'][0]['fullTextAnnotation']['text'];

//             // ✅ Save raw OCR text for debugging
//             OcrInventoryLog::create(['raw_text' => $extractedText]);

//             // ✅ Extract structured data using regex
//             preg_match('/Product:\s*(.+)/', $extractedText, $productMatch);
//             preg_match('/Batch:\s*(\w+)/', $extractedText, $batchMatch);
//             preg_match('/Expiry:\s*(\d{4}-\d{2}-\d{2})/', $extractedText, $expiryMatch);
//             preg_match('/Quantity:\s*(\d+)/', $extractedText, $quantityMatch);
//             preg_match('/Location:\s*(.+)/', $extractedText, $locationMatch);

//             if (!$productMatch || !$batchMatch || !$expiryMatch || !$quantityMatch) {
//                 return response()->json(['message' => '❌ Error: Could not detect required fields.'], 400);
//             }

//             // ✅ Extract values
//             $data = [
//                 'product_name' => trim($productMatch[1]),
//                 'batch_number' => trim($batchMatch[1]),
//                 'expiry_date'  => trim($expiryMatch[1]),
//                 'quantity'     => (int)trim($quantityMatch[1]),
//                 'location'     => $locationMatch[1] ?? null, // If location is missing, set to NULL
//             ];

//             // ✅ Return extracted data to the frontend (not saved yet)
//             return response()->json(['message' => '✅ Data extracted! Please review before saving.', 'data' => $data], 200);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'message' => '❌ Server error: ' . $e->getMessage(),
//                 'line'    => $e->getLine(),
//                 'file'    => $e->getFile()
//             ], 500);
//         }
//     }

//     public function saveInventory(Request $request)
//     {
//         try {
//             // ✅ Validate request data
//             $request->validate([
//                 'product_name' => 'required|string',
//                 'batch_number' => 'required|string',
//                 'expiry_date'  => 'required|date',
//                 'quantity'     => 'required|integer|min:1',
//                 'location'     => 'nullable|string',
//             ]);

//             // ✅ Find or create product
//             $product = Product::firstOrCreate(['generic_name' => $request->product_name]);

//             // ✅ Find location (if provided)
//             $location = $request->location ? Location::firstOrCreate(['province' => $request->location]) : null;

//             // ✅ Add inventory to the database
//             $inventory = Inventory::create([
//                 'location_id'  => $location ? $location->id : null, // Store NULL if no location
//                 'product_id'   => $product->id,
//                 'batch_number' => $request->batch_number,
//                 'expiry_date'  => $request->expiry_date,
//                 'quantity'     => $request->quantity,
//             ]);

//             return response()->json(['message' => '✅ Inventory successfully added!', 'inventory' => $inventory], 200);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'message' => '❌ Server error: ' . $e->getMessage(),
//                 'line'    => $e->getLine(),
//                 'file'    => $e->getFile()
//             ], 500);
//         }
//     }
// }

//ENDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD
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
            $request->validate([
                'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            ]);
    
            $path = $request->file('receipt_image')->store('receipts', 'public');
            $fullPath = public_path("storage/{$path}");
    
            if (!file_exists($fullPath)) {
                return response()->json(['message' => 'Error: File not found in storage.'], 400);
            }
    
            $imageData = base64_encode(file_get_contents($fullPath));
            $apiKey = env('GOOGLE_VISION_API_KEY');
    
            if (!$apiKey) {
                return response()->json(['message' => 'Error: Google API Key is missing.'], 400);
            }
    
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
    
            $extractedText = $visionResult['responses'][0]['fullTextAnnotation']['text'];
            Log::info("OCR Extracted Text: \n" . $extractedText);
    
            OcrInventoryLog::create(['raw_text' => $extractedText]);
    
            // ✅ Improved regex to correctly separate multiple products
            $pattern = '/Product:\s*(.+?)\s*Batch:\s*(.+?)\s*Expiry:\s*([\w\s,]+?)\s*Quantity:\s*(\d+)\s*Location:\s*(.+?)(?=\nProduct:|\z)/s';
    
            preg_match_all($pattern, $extractedText, $matches, PREG_SET_ORDER);
    
            $products = [];
    
            foreach ($matches as $match) {
                // Ensure expiry date is correctly formatted
                $formattedDate = date('Y-m-d', strtotime(trim($match[3])));
    
                $products[] = [
                    'product_name' => trim($match[1]),
                    'batch_number' => trim($match[2]),
                    'expiry_date'  => $formattedDate,
                    'quantity'     => (int) trim($match[4]),
                    'location'     => trim($match[5]),
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
                'products.*.location'     => 'nullable|string',
            ]);
    
            $failedBatches = []; // Store failed batch numbers
    
            foreach ($request->products as $data) {
                // ✅ Check if batch number already exists in inventory
                $existingInventory = Inventory::where('batch_number', $data['batch_number'])->first();
    
                if ($existingInventory) {
                    // ❌ If batch already exists, do not insert
                    $failedBatches[] = $data['batch_number'];
                    continue;
                }
    
                // ✅ Find or create product
                $product = Product::firstOrCreate(['generic_name' => $data['product_name']]);
    
                // ✅ Find or create location
                $location = $data['location'] ? Location::firstOrCreate(['province' => $data['location']]) : null;
    
                // ✅ Add inventory to the database
                Inventory::create([
                    'location_id'  => $location ? $location->id : null, // Store NULL if no location
                    'product_id'   => $product->id,
                    'batch_number' => $data['batch_number'],
                    'expiry_date'  => $data['expiry_date'],
                    'quantity'     => $data['quantity'],
                ]);
            }
    
            // ✅ Return response
            if (!empty($failedBatches)) {
                return response()->json([
                    'message' => '⚠ Some items were not saved because they already exist in inventory.',
                    'failed_batches' => $failedBatches
                ], 409); // 409 Conflict (indicates a duplicate issue)
            }
    
            return response()->json(['message' => '✅ Inventory successfully added!'], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => '❌ Server error: ' . $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ], 500);
        }
    }
    
}    

