<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

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
        // Kirim data profil user login ke semua view
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $profile = Profile::firstOrCreate(['user_id' => Auth::user()->user_id]);
                $view->with('profile', $profile);
            }
        });
    }
}
