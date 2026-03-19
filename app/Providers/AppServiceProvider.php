<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
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
    public function boot()
    {
        View::composer('*', function ($view) {
            $currentUser = Auth::user();
            $role = null;

            if ($currentUser) {
                $role = DB::table('roles')
                    ->join('members', 'roles.id', '=', 'members.role_id')
                    ->where('members.user_id', $currentUser->id)
                    ->select('roles.*')
                    ->first();
            }

            $view->with('role', $role);
        });
    }

}
