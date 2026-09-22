<?php

namespace App\Http\Middleware;

use App\Services\Translation\TranslationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = array_keys(config('app.available_locales', [
            'es' => [],
            'en' => [],
            'pt' => [],
            'it' => [],
            'ar' => [],
        ]));

        $locale = session('locale');

        if (! $locale || ! in_array($locale, $availableLocales, true)) {
            $preferred = $request->getPreferredLanguage($availableLocales);
            $locale = $preferred ?: config('app.locale', 'es');
        }

        App::setLocale($locale);

        TranslationService::resetRequestBudget();

        return $next($request);
    }
}
