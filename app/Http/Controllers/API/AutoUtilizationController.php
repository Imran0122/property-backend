<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AutoUtilizationSetting;
use App\Models\UserCredit;

class AutoUtilizationController extends Controller
{
    // GET /api/auto-utilization
    public function index(Request $request)
    {
        $user     = $request->user();
        $credits  = UserCredit::firstOrCreate(['user_id' => $user->id]);
        $settings = AutoUtilizationSetting::where('user_id', $user->id)->get();

        $categories = [
            [
                'key'           => 'hot-ad',
                'label'         => 'Hot Listing',
                'available'     => $credits->hot_credits,
            ],
            [
                'key'           => 'super-hot-ad',
                'label'         => 'Super Hot Listing',
                'available'     => $credits->super_hot_credits,
            ],
            [
                'key'           => 'refreshment-credits',
                'label'         => 'Refresh Listing',
                'available'     => $credits->refresh_credits,
            ],
        ];

        $result = collect($categories)->map(function ($cat) use ($settings) {
            $setting = $settings->firstWhere('category', $cat['key']);
            return array_merge($cat, [
                'percentage'      => $setting?->percentage ?? 0,
                'scope'           => $setting?->scope ?? 'self',
                'is_active'       => $setting?->is_active ?? false,
                'utilized_credits'=> $setting
                    ? (int) round($cat['available'] * ($setting->percentage / 100))
                    : 0,
            ]);
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'credits'    => [
                    'refresh_credits'   => $credits->refresh_credits,
                    'hot_credits'       => $credits->hot_credits,
                    'super_hot_credits' => $credits->super_hot_credits,
                    'story_credits'     => $credits->story_credits,
                    'photo_credits'     => $credits->photo_credits,
                    'video_credits'     => $credits->video_credits,
                ],
                'settings' => $result->values(),
            ],
        ]);
    }

    // POST /api/auto-utilization/apply
    public function apply(Request $request)
    {
        $request->validate([
            'category'   => 'required|string',
            'percentage' => 'required|integer|min:0|max:100',
            'scope'      => 'required|in:self,agency_wide',
        ]);

        $user    = $request->user();
        $credits = UserCredit::firstOrCreate(['user_id' => $user->id]);

        $availableMap = [
            'hot-ad'               => $credits->hot_credits,
            'super-hot-ad'         => $credits->super_hot_credits,
            'refreshment-credits'  => $credits->refresh_credits,
        ];

        $available = $availableMap[$request->category] ?? 0;

        if ($available === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No credits available for this category. Please purchase credits first.',
            ], 422);
        }

        $utilized = (int) round($available * ($request->percentage / 100));

        AutoUtilizationSetting::updateOrCreate(
            [
                'user_id'  => $user->id,
                'category' => $request->category,
            ],
            [
                'percentage' => $request->percentage,
                'scope'      => $request->scope,
                'is_active'  => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Auto utilization criteria applied successfully.',
            'data'    => [
                'category'        => $request->category,
                'percentage'      => $request->percentage,
                'scope'           => $request->scope,
                'available'       => $available,
                'utilized_credits'=> $utilized,
            ],
        ]);
    }

    // DELETE /api/auto-utilization/{category}
    public function remove(Request $request, $category)
    {
        AutoUtilizationSetting::where('user_id', $request->user()->id)
            ->where('category', $category)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Auto utilization setting removed.',
        ]);
    }
}