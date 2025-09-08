<?php

namespace App\Listeners;

use App\Events\NewMessageReceived; // <-- IMPORT NEW EVENT
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewMessageMail;
use App\Models\SuperAdmin;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\User;

// Implement ShouldQueue for better performance!
class SendNewMessageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\NewMessageReceived  $event
     * @return void
     */
   // In app/Listeners/SendNewMessageNotification.php

public function handle(NewMessageReceived $event)
{
    $conversation = $event->conversation;

    // Find the recipient user model based on type
    $recipient = match ($conversation->receiver_type) {
        'super_admin' => SuperAdmin::find($conversation->receiver_id),
        'admin' => Admin::find($conversation->receiver_id),
        'staff' => Staff::find($conversation->receiver_id),
        'customer' => User::find($conversation->receiver_id),
        default => null,
    };
    
    // Find the sender user model to get their name
    $sender = match ($conversation->sender_type) {
        'super_admin' => SuperAdmin::find($conversation->sender_id),
        'admin' => Admin::find($conversation->sender_id),
        'staff' => Staff::find($conversation->sender_id),
        'customer' => User::find($conversation->sender_id),
        default => null,
    };

    // Determine the sender's specific name
    $senderName = 'Unknown';
    if ($sender) {
         $senderName = match ($conversation->sender_type) {
            'super_admin' => $sender->s_admin_username,
            'admin' => $sender->username,
            'staff' => $sender->staff_username,
            'customer' => $sender->name,
            default => 'Unknown',
        };
    }

    // Check if recipient exists and has an email
    if ($recipient && !empty($recipient->email)) {
        $messageContent = $conversation->message ?: 'Sent a file.';

        Mail::to($recipient->email)
            ->send(new NewMessageMail($senderName, $messageContent));
    }
}

}