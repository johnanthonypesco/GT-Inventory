<?php

namespace App\Listeners;

use App\Models\BlockedIp;
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
            
            $logData = HistorylogController::failedLoginLog($email);
            $ipAddress = $logData['ip'];
            $locationString = $logData['location'];
            $key = 'login-attempt:' . $ipAddress;
            RateLimiter::hit($key, 600); 

            if (RateLimiter::tooManyAttempts($key, 10)) {
                $notificationKey = 'notification-sent:' . $ipAddress;
                if (RateLimiter::attempt($notificationKey, 1, fn() => true, 600)) {
                    $superAdmins = SuperAdmin::all();
                    foreach ($superAdmins as $superAdmin) {
                        Mail::to($superAdmin->email)->send(
                            new BruteForceAlert($ipAddress, $locationString, $email)
                        );
                    }
                }

                // The blocking command here is correct. It runs after 5 failed attempts.
                BlockedIp::firstOrCreate(
                    ['ip_address' => $ipAddress],
                    [
                        'reason' => 'Brute-force login attempt detected.',
                        'blocked_by_email' => 'System',
                    ]
                );
            }
        } elseif ($event instanceof Logout) {
            if ($event->user) {
                HistorylogController::logoutLog($event->user);
            }
        }
    }
}