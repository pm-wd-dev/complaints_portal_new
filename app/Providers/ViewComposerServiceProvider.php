<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        View::composer('admin.complaints', function ($view) {
            $view->with('respondents', User::where('role', 'respondent')->get(['id', 'name', 'email']));
        });
    }
}
