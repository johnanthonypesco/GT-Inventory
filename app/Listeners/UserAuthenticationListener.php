<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Http\Controllers\Admin\HistorylogController;

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
        // Check if the event is a successful Login
        if ($event instanceof Login) {
            // A user successfully logged in, so we log it.
            HistorylogController::loginLog($event->user);

        } 
        // Check if the event is a Failed login attempt
        elseif ($event instanceof Failed) {
            // A user failed to log in. We get the email they tried to use.
            if (!empty($event->credentials['email'])) {
                $email = $event->credentials['email'];
                HistorylogController::failedLoginLog($email);
            }

        } 
        // Check if the event is a Logout
        elseif ($event instanceof Logout) {
            // A user logged out. We check if the user object exists before logging.
            if ($event->user) {
                HistorylogController::logoutLog($event->user);
            }
        }
    }
}