<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Handle the secure, server-side request for AI trend analysis using OpenAI.
     */
    public function getAiAnalysis(array $validated)
    {
        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            Log::error('OPENAI_API_KEY is not set in .env file.');
            return response()->json(['error' => 'AI analysis is not configured on the server.'], 500);
        }

        $productName = $validated['product_name'];

        // --- FIX: Use traditional function syntax for map ---
        $dataString = collect($validated['seasonal_data'])->map(function ($item) {
            return "- {$item['label']}: {$item['data']}";
        })->join("\n");

        $systemPrompt = "You are a helpful data analyst for a public health clinic in the Philippines. Your tone is professional, insightful, and concise. Do not use markdown (like *, #, or lists). Respond in plain paragraphs.";

        $userQuery = "Analyze the following monthly dispensation data (items dispensed per month) for the product '$productName':\n\n$dataString\n\n";

        if (!empty($validated['compare_product_name'])) {
            $compareName = $validated['compare_product_name'];
            // --- FIX: Use traditional function syntax for map ---
            $compareString = collect($validated['compare_data'])->map(function ($item) {
                return "- {$item['label']}: {$item['data']}";
            })->join("\n");
            $userQuery .= "For comparison, here is the data for '$compareName':\n\n$compareString\n\n";
            $userQuery .= "Please analyze both products. Compare their trends, note any seasonal peaks or similarities (e.g., 'both peak around August'), explain potential reasons for the trends (e.g., related to seasons), and provide a simple 1-sentence predictive recommendation for managing stock for *each* product based on this comparison.";
        } else {
            $userQuery .= "Based ONLY on the data provided, please provide the following in plain paragraphs:\n1. A brief summary of any seasonal trends or significant peaks/troughs you observe.\n2. An insight into potential reasons *why* these trends might be happening (e.g., linking peaks to rainy season, flu season, etc.).\n3. A simple 1-sentence predictive recommendation for managing stock (e.g., 'Prepare for higher demand around August-October.').";
        }

        $apiUrl = "https://api.openai.com/v1/chat/completions";

        $payload = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userQuery]
            ],
            'temperature' => 0.6,
            'max_tokens' => 500,
        ];

        try {
            $response = Http::withToken($apiKey)
                            ->timeout(60)
                            ->post($apiUrl, $payload);

            if (!$response->successful()) {
                Log::error('OpenAI API request failed', ['status' => $response->status(), 'body' => $response->json()]);
                $errorBody = $response->json('error.message', 'The AI service failed to respond.');
                return response()->json(['error' => $errorBody], $response->status());
            }

            $text = data_get($response->json(), 'choices.0.message.content');

            if ($text) {
                $cleanedText = preg_replace('/^\s*[\*\-\d]+\.?\s*/m', '', $text);
                return response()->json(['analysis' => nl2br(trim($cleanedText))]);
            }

            $finishReason = data_get($response->json(), 'choices.0.finish_reason');
            Log::error('OpenAI API gave no content', ['reason' => $finishReason, 'response' => $response->json()]);

            if ($finishReason === 'content_filter') {
                return response()->json(['error' => 'The AI analysis was blocked due to content filters.'], 400);
            }

            return response()->json(['error' => 'No valid response received from the AI analysis service.'], 500);

        } catch (Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection Error calling OpenAI API: ' . $e->getMessage());
            return response()->json(['error' => 'Could not connect to the AI analysis service. Please check the network connection.'], 503);
        } catch (\Exception $e) {
            Log::error('Error calling OpenAI API: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred while contacting the AI analysis service.'], 500);
        }
    }
}
