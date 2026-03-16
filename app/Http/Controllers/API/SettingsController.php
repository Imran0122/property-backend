<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function getSettings(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'landline' => $user->landline,
                    'whatsapp' => $user->whatsapp,
                    'city_id' => $user->city_id,
                    'address' => $user->address,
                    'profile_image' => $user->profile_image,
                    'profile_image_url' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
                ],
                'preferences' => [
                    'email_notifications' => (bool) $user->email_notifications,
                    'newsletters' => (bool) $user->newsletters,
                    'automated_reports' => (bool) $user->automated_reports,
                    'currency' => $user->currency,
                    'area_unit' => $user->area_unit,
                ]
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile' => ['nullable', 'string', 'max:25'],
            'landline' => ['nullable', 'string', 'max:25'],
            'whatsapp' => ['nullable', 'string', 'max:25'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:1000'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'update_all_properties' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        // फिलहाल checkbox ko sirf accept kar rahe hain, logic baad me add hoga
        unset($data['update_all_properties']);

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $user->fresh(),
                'profile_image_url' => $user->fresh()->profile_image ? asset('storage/' . $user->fresh()->profile_image) : null,
            ]
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'email_notifications' => ['sometimes', 'boolean'],
            'newsletters' => ['sometimes', 'boolean'],
            'automated_reports' => ['sometimes', 'boolean'],
            'currency' => ['sometimes', 'string', 'max:10'],
            'area_unit' => ['sometimes', 'string', 'max:10'],
        ]);

        $user->update($data);
        $freshUser = $user->fresh();

        return response()->json([
            'status' => true,
            'message' => 'Preferences updated successfully',
            'data' => [
                'preferences' => [
                    'email_notifications' => (bool) $freshUser->email_notifications,
                    'newsletters' => (bool) $freshUser->newsletters,
                    'automated_reports' => (bool) $freshUser->automated_reports,
                    'currency' => $freshUser->currency,
                    'area_unit' => $freshUser->area_unit,
                ]
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($data['old_password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Old password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}