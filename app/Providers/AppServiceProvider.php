<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

use App\Models\Conversation;
use App\Models\SuperAdmin;
use App\Models\Admin;
use App\Models\Staff;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // COMMENT THIS IF YOU NOT USING CLOUD FLARE TUNNEL
        // This is to force HTTPS when using Cloudflare Tunnel
        // It checks the X-Forwarded-Proto header to determine if the request is secure
        // and sets the URL scheme accordingly.
        // If you are not using Cloudflare Tunnel, you can comment this section out.
        // 👇 Detect if forwarded protocol is HTTPS (from Cloudflare Tunnel)
        $hosting = env('APP_HOSTING');

        if ($hosting === 'cloudflare' && request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        } elseif ($hosting === 'hostinger') {
            // Optional: Force HTTPS directly if you want
            if (request()->secure()) {
                URL::forceScheme('https');
            }
        }
        View::composer('*', function ($view) {
            $currentUser = Auth::user();

            $unreadMessagesAdmin = 0;
            $unreadMessagesSuperAdmin = 0;
            $unreadMessagesStaff = 0;
            $adminsidebar_counter = 0;

            if ($currentUser instanceof SuperAdmin) {
                $unreadMessagesSuperAdmin = Cache::remember('unread_messages_superadmin', 10, function () use ($currentUser) {
                    return Conversation::where('is_read', false)
                        ->where('receiver_type', 'super_admin')
                        ->where('receiver_id', $currentUser->id)
                        ->count();
                });
                $adminsidebar_counter = $unreadMessagesSuperAdmin;
            } elseif ($currentUser instanceof Admin) {
                $unreadMessagesAdmin = Cache::remember('unread_messages_admin', 10, function () use ($currentUser) {
                    return Conversation::where('is_read', false)
                        ->where('receiver_type', 'admin')
                        ->where('receiver_id', $currentUser->id)
                        ->count();
                });
                $adminsidebar_counter = $unreadMessagesAdmin;
            } elseif ($currentUser instanceof Staff) {
                $unreadMessagesStaff = Cache::remember('unread_messages_staff', 10, function () use ($currentUser) {
                    return Conversation::where('is_read', false)
                        ->where('receiver_type', 'staff')
                        ->where('receiver_id', $currentUser->id)
                        ->count();
                });
                $adminsidebar_counter = $unreadMessagesStaff;
            }

            $view->with([
                'unreadMessagesAdmin' => $unreadMessagesAdmin,
                'unreadMessagesSuperAdmin' => $unreadMessagesSuperAdmin,
                'unreadMessagesStaff' => $unreadMessagesStaff,
                'adminsidebar_counter' => $adminsidebar_counter,
                'currentUser' => $currentUser,
            ]);
        });
    }
}
