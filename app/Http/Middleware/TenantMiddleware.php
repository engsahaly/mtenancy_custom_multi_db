<?php

namespace App\Http\Middleware;

use App\Services\TenantDatabaseService;
use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check for subdomain
        $subdomain = $request->getHost();

        // check if subdmoin exists and is not the main url
        if ($subdomain && $subdomain != 'mtenancy_custom_multi_db.test') {
            $tenant = Tenant::where('subdomain', $subdomain)->first();
            
            if ($tenant && config('database.connections.tenant.database') != $tenant->database) {
                // connect to corresponding database
                (new TenantDatabaseService())->connectToDB($tenant);
                // migrate to the new databse
                (new TenantDatabaseService())->migrateToDB($tenant);
            } else {
                abort(404);
            }
        }


        return $next($request);
    }
}
