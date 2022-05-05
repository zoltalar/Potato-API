<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Models\Language as LanguageModel;
use Closure;
use Illuminate\Http\Request;

class Language
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('X-language')) {
            $code = $request->header('X-language');

            if (in_array($code, LanguageModel::codes())) {
                app()->setLocale($code);
            }
        }

        return $next($request);
    }
}
