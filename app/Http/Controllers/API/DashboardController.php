<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Lead;
use App\Models\Inquiry;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\Package;
use App\Models\PropertyImage;

class DashboardController extends Controller
{

    /**
     * GET /api/dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // =========================
        // PROPERTY STATS
        // =========================

        $forSale = Property::where('user_id',$user->id)
                    ->where('purpose','sale')
                    ->count();

        $forRent = Property::where('user_id',$user->id)
                    ->where('purpose','rent')
                    ->count();

        $featured = Property::where('user_id',$user->id)
                    ->where('is_featured',1)
                    ->count();

        $hot = Property::where('user_id',$user->id)
                ->where('is_hot',1)
                ->count();

        $superHot = Property::where('user_id',$user->id)
                ->where('is_super_hot',1)
                ->count();

        $total = Property::where('user_id',$user->id)->count();


        // =========================
        // ANALYTICS
        // =========================

        $leads = Lead::where('user_id',$user->id)->count();

        $inquiries = Inquiry::where('user_id',$user->id)->count();

        $messages = Message::where('receiver_id',$user->id)->count();

        // Future analytics (placeholders)
        $views = 0;
        $clicks = 0;
        $calls = 0;
        $whatsapp = 0;
        $sms = 0;
        $emails = 0;


        // =========================
        // SUBSCRIPTION
        // =========================

        $subscription = Subscription::where('user_id',$user->id)
                        ->where('status','active')
                        ->first();

        $package = null;

        if($subscription){
            $package = Package::find($subscription->package_id);
        }

        // =========================
        // QUOTA / CREDIT SYSTEM
        // =========================

        $listingQuota = $package ? $package->property_limit : 0;
        $featuredQuota = $package ? $package->featured_limit : 0;

        $usedListings = $subscription ? $subscription->used_properties : 0;
        $usedFeatured = $subscription ? $subscription->used_featured : 0;


        // =========================
        // RECENT PROPERTIES
        // =========================

        $recentProperties = Property::where('user_id',$user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($property) {

                $image = PropertyImage::where('property_id',$property->id)
                        ->where('is_primary',1)
                        ->first();

                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'price' => $property->price,
                    'purpose' => $property->purpose,
                    'created_at' => $property->created_at,
                    'image' => $image ? $image->image_path : null
                ];
            });


        // =========================
        // RESPONSE
        // =========================

        return response()->json([
            'status' => true,

            'stats' => [
                'for_sale' => $forSale,
                'for_rent' => $forRent,
                'featured' => $featured,
                'hot' => $hot,
                'super_hot' => $superHot,
                'total_properties' => $total
            ],

            'analytics' => [
                'views' => $views,
                'clicks' => $clicks,
                'leads' => $leads,
                'calls' => $calls,
                'whatsapp' => $whatsapp,
                'sms' => $sms,
                'emails' => $emails,
                'messages' => $messages,
                'inquiries' => $inquiries
            ],

            'quota' => [
                'listing_quota' => $listingQuota,
                'used_listings' => $usedListings,
                'available_listings' => $listingQuota - $usedListings,

                'featured_quota' => $featuredQuota,
                'used_featured' => $usedFeatured,
                'available_featured' => $featuredQuota - $usedFeatured,

                'current_plan' => $package ? $package->name : null
            ],

            'subscription' => $subscription ? [
                'package_name' => $package ? $package->name : null,
                'property_limit' => $package ? $package->property_limit : 0,
                'featured_limit' => $package ? $package->featured_limit : 0,
                'used_properties' => $subscription->used_properties,
                'used_featured' => $subscription->used_featured
            ] : null,

            'recent_properties' => $recentProperties
        ]);
    }


    /**
     * GET /api/my-properties
     */
    public function myProperties(Request $request)
    {
        $properties = Property::where('user_id', $request->user()->id)
            ->with('images')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $properties
        ]);
    }
}