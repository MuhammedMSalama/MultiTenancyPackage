<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Update Register Functionality | Tenant
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

        // Update Register Functionality | Tenant
        if ($request->has('tenant')) {
            // Update Register Functionality | Tenant
            $tenant = Tenant::create([
                'id' => $data['tenant'],
                'name' => $data['tenant'],
                'user_id' => $user->id,
            ]);

            // Tenant Domains
            $tenant->domains()->create([
                'domain' => $data['tenant'],
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        if (isset($tenant)) {
            return redirect('http://' . $data['tenant'] . '.teams.test/login');
        }

        return redirect(route('dashboard', absolute: false));
    }
}
