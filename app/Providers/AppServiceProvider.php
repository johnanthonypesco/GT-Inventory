<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\User;
use App\Models\SuperAdmin;
use App\Models\Staff;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use App\Models\Conversation; // Import the Conversation model

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
        // Define morph map for polymorphic relations
        Relation::morphMap([
            'user' => User::class,
            'super_admin' => SuperAdmin::class,
            'staff' => Staff::class, // 🔹 Siguraduhin may staff dito!
        ]);

        // Share total unread messages with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $totalUnreadMessages = Conversation::where('receiver_id', Auth::id())
                    ->where('receiver_type', 'customer')
                    ->where('is_read', 0)
                    ->count();
                $view->with('totalUnreadMessages', $totalUnreadMessages);
            } else {
                $view->with('totalUnreadMessages', 0);
            }
        });
    }
}