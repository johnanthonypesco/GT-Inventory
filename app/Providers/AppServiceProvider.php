<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
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
        // Share total unread messages with all views
        View::composer('*', function ($view) {
            // Kunin ang current user
            $currentUser = Auth::user();

            // I-initialize ang mga variables
            $unreadMessagesAdmin = 0;
            $unreadMessagesSuperAdmin = 0;
            $unreadMessagesStaff = 0;
            $adminsidebar_counter = 0;

            // I-check kung sino ang naka-login
            if ($currentUser instanceof SuperAdmin) {
                // Para sa SuperAdmin, bilangin lang ang unread messages na para sa SuperAdmin
                $unreadMessagesSuperAdmin = Conversation::where('is_read', false)
                    ->where('receiver_type', 'super_admin')
                    ->where('receiver_id', $currentUser->id)
                    ->count();

                // Total unread messages para sa sidebar
                $adminsidebar_counter = $unreadMessagesSuperAdmin;
            } elseif ($currentUser instanceof Admin) {
                // Para sa Admin, bilangin lang ang unread messages na para sa Admin
                $unreadMessagesAdmin = Conversation::where('is_read', false)
                    ->where('receiver_type', 'admin')
                    ->where('receiver_id', $currentUser->id)
                    ->count();

                // Total unread messages para sa sidebar
                $adminsidebar_counter = $unreadMessagesAdmin;
            } elseif ($currentUser instanceof Staff) {
                // Para sa Staff, bilangin lang ang unread messages na para sa Staff
                $unreadMessagesStaff = Conversation::where('is_read', false)
                    ->where('receiver_type', 'staff')
                    ->where('receiver_id', $currentUser->id)
                    ->count();

                // Total unread messages para sa sidebar
                $adminsidebar_counter = $unreadMessagesStaff;
            }

            // I-share ang data sa view
            $view->with([
                'unreadMessagesAdmin' => $unreadMessagesAdmin,
                'unreadMessagesSuperAdmin' => $unreadMessagesSuperAdmin,
                'unreadMessagesStaff' => $unreadMessagesStaff,
                'adminsidebar_counter' => $adminsidebar_counter, // I-share ang total unread messages para sa sidebar
                'currentUser' => $currentUser, // I-share ang current user para magamit sa view
            ]);
        });
    }
}