<?php

namespace App\Providers;

use App\Models\Debt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Sidebar badge + bell dropdown: open debts that are overdue or due within a week.
        View::composer(['layouts.app', 'layouts::app'], function ($view) {
            $view->with([
                'openDebtCount' => Debt::open()->count(),
                'alerts' => Debt::open()->with('person')->withPaid()
                    ->whereNotNull('due_on')
                    ->whereDate('due_on', '<=', today()->addDays(7))
                    ->orderBy('due_on')
                    ->limit(6)
                    ->get(),
            ]);
        });
    }
}
