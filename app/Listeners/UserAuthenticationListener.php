<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\Admin\HistorylogController;
use App\Mail\BruteForceAlert;
use App\Models\SuperAdmin;

class UserAuthenticationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle user authentication events.
     */
    public function handle(object $event): void
    {
        if ($event instanceof Login) {
            HistorylogController::loginLog($event->user);

        } elseif ($event instanceof Failed) {
            $email = $event->credentials['email'] ?? 'not_provided';

            // 1. Call the logger AND capture the returned data into $logData
            $logData = HistorylogController::failedLoginLog($email);

            // 2. Extract the IP and location from the data
            $ipAddress = $logData['ip'];
            $locationString = $logData['location'];

            // 3. Use the data for the Brute-Force Detection
            $key = 'login-attempt:' . $ipAddress;
            RateLimiter::hit($key, 600); 

            if (RateLimiter::tooManyAttempts($key, 5)) {
                $notificationKey = 'notification-sent:' . $ipAddress;
                if (RateLimiter::attempt($notificationKey, 1, fn() => true, 600)) {
                    $superAdmins = SuperAdmin::all();
                    foreach ($superAdmins as $superAdmin) {
                        Mail::to($superAdmin->email)->send(
                            new BruteForceAlert($ipAddress, $locationString, $email)
                        );
                    }
                }
            }
        } elseif ($event instanceof Logout) {
            if ($event->user) {
                HistorylogController::logoutLog($event->user);
            }
        }
    }
}