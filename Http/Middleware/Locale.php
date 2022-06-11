<?php

namespace App\Http\Middleware;

/**
 * Class Locale
 * @package App\Http\Middleware
 */
class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $locale = $request->get(config('app.key_param_locale_in_route'), config('app.locale'));

        if (!array_key_exists($locale, config('app.locales'))) {
            $locale = config('app.locale');
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return $next($request);
    }
}
