<?php

namespace App\Http\Controllers\Admin;

use App\Models\Historylogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; 


class HistorylogController extends Controller
{
    /**
     * ✅ PRIVATE HELPER: A single, private method to create any log entry.
     * All other public methods will call this one. This ensures no code is repeated.
     */
    private static function log($event, $description, $userEmail)
    {
        Historylogs::create([
            'event'       => $event,
            'description' => $description,
            'user_email'  => $userEmail,
            'created_at'  => now(),
        ]);
    }

    /**
     * ✅ NEW: Logs a successful user login.
     * Call this from your TwoFactorController after successful authentication.
     */
    public static function loginLog($user)
    {
        $role = ucfirst($user->role ?? 'user');
        $description = "$role '{$user->email}' logged in successfully.";
        self::log('Login', $description, $user->email);
    }

    /**
     * ✅ NEW: Logs a user logout.
     * Call this from the destroy() method in your auth controllers.
     */
    public static function logoutLog($user)
    {
        $role = ucfirst($user->role ?? 'user');
        $description = "$role '{$user->email}' logged out.";
        self::log('Logout', $description, $user->email);
    }

     public static function failedLoginLog($email)
    {

         if (App::environment('local')) {
        // --- FOR LOCAL DEVELOPMENT ---
        // This block runs only when APP_ENV in your .env file is 'local'.
        // It asks an external service for your public IP for testing.
        $ipAddress = Http::get('https://api.ipify.org')->body();
    } else {
        // --- FOR PRODUCTION / LIVE SERVER ---
        // This block runs for any other environment (e.g., 'production').
        // It gets the real IP address of the website visitor.
        $ipAddress = request()->ip();
    }
        $locationString = 'Location lookup failed'; // Default value

        // ✅ 2. MAKE THE DIRECT API CALL
        // Note: For local development with IP 127.0.0.1, this will fail, which is normal.
        $response = Http::get("http://ip-api.com/json/{$ipAddress}");

        // ✅ 3. CHECK THE RESPONSE AND FORMAT THE LOCATION STRING
        if ($response->successful() && $response->json('status') === 'success') {
            // The API call was successful.
            $data = $response->json();
            $locationString = "{$data['city']}, {$data['country']}";
        }
        
        // ✅ 4. CREATE THE LOG ENTRY WITH THE LOCATION DATA
        $description = "Failed login attempt for email '{$email}'.";
        $actionBy = "Attempt by: {$ipAddress} ({$locationString})";
        
        self::log('Failed Login', $description, $actionBy); 
         return [
        'ip' => $ipAddress,
        'location' => $locationString
    ];
    }
    /**
     * ✅ REPLACEMENT METHOD: This one public method replaces ALL your old repetitive methods.
     * (replaces addproductlog, editproductlog, addaccountlog, etc.)
     *
     * Example Usage:
     * HistorylogController::add('Add', 'User added a new product: XYZ');
     * HistorylogController::add('Approve', 'Review for ABC was approved.');
     */
    public static function add($event, $description)
    {
        // Gets the currently authenticated user's email, defaults to 'System' if none found.
        $userEmail = auth()->user()->email ?? 'System';
        self::log($event, $description, $userEmail);
    }

    // --- Your display and search functions remain the same ---

    public function showHistorylog(Request $request)
    {
        $historylogs = Historylogs::orderBy('created_at', 'desc')->paginate(10);
        $historylogs->withPath(route('admin.historylog.search'));
        
        // Using compact() is a cleaner way to pass data to a view
        return view('admin.historylog', compact('historylogs'));
    }

    public function searchHistorylog(Request $request)
    {
        $query = Historylogs::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('event', 'like', '%' . $search . '%')
                  ->orWhere('user_email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('event') && $request->input('event') != 'All') {
            $event = $request->input('event');
            $query->where('event', $event);
        }

        $historylogs = $query->orderBy('created_at', 'desc')->paginate(10);
        $historylogs->appends($request->all());

        return view('admin.partials.historylog_table', compact('historylogs'));
    }
}