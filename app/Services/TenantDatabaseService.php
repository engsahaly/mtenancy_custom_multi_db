<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TenantDatabaseService
{
    public function createDB($tenant)
    {
        DB::statement("CREATE DATABASE " . $tenant->database);
    }
    
    public function connectToDb($tenant)
    {
        Config::set('database.connections.tenant.database', $tenant->database);
        DB::purge('tenant');
        DB::reconnect('tenant');
        Config::set('database.default', 'tenant');
    }
    
    public function migrateToDb($tenant)
    {
        Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
        ]);
    }
}
