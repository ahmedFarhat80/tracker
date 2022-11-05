<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next , ...$locales)
    {

        // default local language
        $locale = app()->setLocale('ar');

        // read the language from the request header
        if ($language = $request->header('Accept-Language')) {
            // check the languages defined is supported
            if (!in_array($language, app()->config->get('app.supported_languages'))) {
                // respond with error
                return errorMessage(__('dashboard.lang_not_found'), 403);
            }

            app()->setLocale($language);
        }
        return $next($request);
    }
}
