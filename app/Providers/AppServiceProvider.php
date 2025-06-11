<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Conversation;
use App\Models\SuperAdmin;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\ScannedQrCode;
use App\Models\ExclusiveDeal;
use Illuminate\Support\Facades\DB;

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
        View::composer('*', function ($view) {
            // Get the current user
            $currentUser = Auth::user();

            // Initialize variables
            $unreadMessagesAdmin = 0;
            $unreadMessagesSuperAdmin = 0;
            $unreadMessagesStaff = 0;
            $adminsidebar_counter = 0;

            // Check who is logged in
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

            
            // Share the data with the view
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
