<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $setting = Setting::first();

        if (!$setting) {
            $setting = Setting::create([
                'site_name' => 'Property Site',
                'support_email' => 'support@propertysite.com',
                'currency' => 'PKR',
                'country' => 'Pakistan',
                'website_url' => 'https://propertysite.com',
                'site_description' => 'Pakistan’s premium property platform for buyers, sellers, agents and developers.',
                'auto_approve_verified_agents' => false,
                'require_image_validation' => true,
                'enable_duplicate_listing_detection' => true,
                'manual_approval_featured_listings' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'general' => [
                    'site_name' => $setting->site_name,
                    'support_email' => $setting->support_email,
                    'currency' => $setting->currency,
                    'country' => $setting->country,
                    'website_url' => $setting->website_url,
                    'site_description' => $setting->site_description,
                ],
                'listing_rules' => [
                    'auto_approve_verified_agents' => $setting->auto_approve_verified_agents,
                    'require_image_validation' => $setting->require_image_validation,
                    'enable_duplicate_listing_detection' => $setting->enable_duplicate_listing_detection,
                    'manual_approval_featured_listings' => $setting->manual_approval_featured_listings,
                ]
            ]
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'support_email' => 'required|email|max:255',
            'currency' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'website_url' => 'nullable|url|max:255',
            'site_description' => 'nullable|string',

            'auto_approve_verified_agents' => 'required|boolean',
            'require_image_validation' => 'required|boolean',
            'enable_duplicate_listing_detection' => 'required|boolean',
            'manual_approval_featured_listings' => 'required|boolean',
        ]);

        $setting = Setting::first();

        if (!$setting) {
            $setting = new Setting();
        }

        $setting->fill($validated);
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => [
                'general' => [
                    'site_name' => $setting->site_name,
                    'support_email' => $setting->support_email,
                    'currency' => $setting->currency,
                    'country' => $setting->country,
                    'website_url' => $setting->website_url,
                    'site_description' => $setting->site_description,
                ],
                'listing_rules' => [
                    'auto_approve_verified_agents' => $setting->auto_approve_verified_agents,
                    'require_image_validation' => $setting->require_image_validation,
                    'enable_duplicate_listing_detection' => $setting->enable_duplicate_listing_detection,
                    'manual_approval_featured_listings' => $setting->manual_approval_featured_listings,
                ]
            ]
        ]);
    }
}