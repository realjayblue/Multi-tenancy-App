<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $subDomain = explode('.', $request->getHost())[0];
        $tenant = Tenant::where('name', $subDomain)->first();

        //user is not logged in yet so ignore
        if (!Auth::check()) {
            return $next($request);
        }

        if ($subDomain && !$tenant) {
            $this->logout($request);

            return redirect()->route('login', ['tenant' => 'default'])->with('access_denied', true);
        }
        $request->session()->put('tenant', $tenant);

        //Verify the user can access domain

        $canAccess = $request->user()->tenant->name === $subDomain;

        if (!$canAccess) {
            $this->logout($request);
            return redirect()->route('login', ['tenant' => $tenant->name])->with('access_denied', true);
        }

        return $next($request);
    }

    protected function logout($request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }
}
