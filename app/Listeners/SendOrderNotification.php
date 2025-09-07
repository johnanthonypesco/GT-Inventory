<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Admin;
use App\Models\SuperAdmin;
use App\Mail\OrderNotificationMail;
use Illuminate\Support\Facades\Mail;

class SendOrderNotification implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(OrderPlaced $event): void
    {
        $orderDetails = $event->orderDetails;

        // Get all admins and superadmins
        $admins = Admin::all();
        $superadmins = SuperAdmin::all();

        // Send email to all admins
        foreach ($admins as $admin) {
            if ($admin->email) {
                Mail::to($admin->email)->send(new OrderNotificationMail($orderDetails));
            }
        }

        // Send email to all superadmins
        foreach ($superadmins as $superadmin) {
            if ($superadmin->email) {
                Mail::to($superadmin->email)->send(new OrderNotificationMail($orderDetails));
            }
        }
    }
}