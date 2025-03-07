<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\User;
use App\Models\SuperAdmin;
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
        Relation::morphMap([
            'user' => User::class,
            'super_admin' => SuperAdmin::class,
            'staff' => Staff::class, // 🔹 Siguraduhin may staff dito!
        ]);
    }
}
