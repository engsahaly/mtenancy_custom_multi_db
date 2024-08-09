<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tenant' => ['sometimes', 'required', 'string', 'max:255', 'unique:tenants,name'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);        

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->tenant) {
            // create tenant record
            $tenant = Tenant::create([
                'name' => $data['tenant'],
                'subdomain' => str_replace(' ', '', $data['tenant']) . '.mtenancy_custom_multi_db.test',
                'database' => str_replace(' ', '', $data['tenant']),
                'user_id' => $user->id
            ]);
    
            // create new database
            (new TenantDatabaseService())->createDB($tenant);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
