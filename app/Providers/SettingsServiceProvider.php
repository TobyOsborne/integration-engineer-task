<?php
/**
 * A service provider to inject setting variables into the different pages.
 * I decided to use service provider over adding them direct in the route registration,
 * to provide a way to inject variables into multiple pages. Without repetative code.
 * (Though I recognise it's a overkill in this use-case,since only one variable would be used).
 */

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('subscribers', function ($view) {
            $view->with(array(
                'per_page'=>Setting::getSubscribersPerPage()
            ));
        });

        view()->composer('settings', function ($view) {
            $view->with(array(
                'has_key'=>Setting::hasAPIKey()
            ));
        });
    }
}
