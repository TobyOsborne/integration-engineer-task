<?php

/**
 * A middleware to determine if the MailerLite API key is set.
 * If not then redirect to the page to the settings page.
 */
namespace App\Http\Middleware;

use Closure;

use App\Models\Setting;
use Illuminate\Http\Request;

class EnsureKeyIsSet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // check if the API key is set.
        $has_key = Setting::hasAPIKey();

        if (!$has_key) {
            // if the key isn't set, redirect to the settings page.
            return redirect('/settings');
        }
        
        return $next($request);
    }
}
