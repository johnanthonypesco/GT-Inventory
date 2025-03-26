<?php

namespace App\Services;

use Log;
use Illuminate\Support\Facades\Http;

class SmsService
{
    public function send($to, $message)
    {

        
        // dd("SmsService called", $to, $message);
        $response = Http::post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => env('SEMAPHORE_API_KEY'),
            'number' => $to,
            'message' => $message,
            'sendername' => '' // Optional
        ]);
                //  dd("SmsService called", $to, $message);

        // dd($response->status(), $response->body());
        if ($response->successful()) {
            return true;
                    //  dd($response->status(), $response->body());

        }

        Log::error('SMS failed', ['response' => $response->body()]);
        return false;
    }
}
