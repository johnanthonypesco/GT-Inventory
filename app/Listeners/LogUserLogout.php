<?php

namespace App\Listeners;

use App\Models\HistoryLog;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;
        $ip = request()->ip();
        $agent = request()->userAgent();

        $description = <<<DESC
User {$user->name} has logged out.
IP: {$ip}
Browser: {$agent}
DESC;

        HistoryLog::create([
            'action' => 'USER LOGOUT',
            'description' => $description,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'metadata' => [
                'ip' => $ip,
                'agent' => $agent,
            ],
        ]);
    }
}
