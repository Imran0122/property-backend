<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Agency;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:30',
            'is_agent' => 'nullable|boolean',
            'newsletters' => 'nullable|boolean',
            'agency_name' => 'nullable|string|max:255',
            'city_id' => 'nullable|integer|exists:cities,id',
        ]);

        $isAgent = (bool) ($validated['is_agent'] ?? false);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $isAgent ? 'agent' : 'user',
            'is_admin' => 0,
            'is_agent' => $isAgent ? 1 : 0,
            'status' => 'active',
            'agency_name' => $validated['agency_name'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'email_notifications' => false,
            'newsletters' => (bool) ($validated['newsletters'] ?? false),
            'automated_reports' => false,
            'currency' => 'MAD',
            'area_unit' => 'm²',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;





// Agency create karo agar agent hai
if ($isAgent && !empty($request->agency_name)) {
    $baseSlug = Str::slug($request->agency_name);
    $slug = $baseSlug . '-' . $user->id;

    $agency = Agency::create([
        'user_id' => $user->id,
        'name'    => $request->agency_name,
        'email'   => $request->agency_email ?? $user->email,
        'address' => $request->agency_address ?? '',
        'phone'   => $user->phone ?? '',
        'slug'    => $slug,
        'status'  => 'pending',
    ]);

    // User ko agency se link karo
    $user->update(['agency_id' => $agency->id]);
}





try {
    \Illuminate\Support\Facades\Mail::to(config('mail.from.address'))->send(
        new \App\Mail\AdminSubmissionMail(
            $isAgent ? 'agent' : 'user',
            $user->name,
            $user->email,
            $user->name,
            [
                'Phone' => $user->phone ?? '—',
                'Role'  => $isAgent ? 'Agent' : 'User',
            ]
        )
    );
} catch (\Exception $e) {}











        return response()->json([
            'success' => true,
            'message' => $isAgent ? 'Agent registered successfully' : 'User registered successfully',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', strtolower(trim($validated['email'])))->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $status = strtolower((string) ($user->status ?? 'active'));

        if (in_array($status, ['suspended', 'inactive'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not active. Please contact support.',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}