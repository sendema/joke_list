<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->hasActiveSubscription()) {
            return redirect()->route('subscription.form')
                ->with('info', 'Для доступа к этому разделу необходима активная подписка');
        }

        return $next($request);
    }
}
