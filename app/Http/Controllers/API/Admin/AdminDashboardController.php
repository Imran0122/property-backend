<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Models\Blog;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function stats()
    {
        $totalProperties = Property::count();
        $pendingApproval = Property::where('status', 'pending')->count();
        $registeredUsers = User::count();
        $publishedArticles = Blog::where('status', 'Published')->count();

        $approvedListings = Property::where('status', 'active')->count();
        $rejectedFlagged = Property::where('status', 'rejected')->count();
        $featuredInventory = Property::where('is_featured', 1)->count();
        $monthlyRevenue = Invoice::where('status', 'paid')->sum('amount');

        // Placeholder until exact source tables are wired
        $portalViews = 0;
        $calls = 0;
        $emails = 0;

        $priorities = [
            [
                'title' => 'Pending properties need review',
                'count' => $pendingApproval,
            ],
            [
                'title' => 'Rejected listings',
                'count' => $rejectedFlagged,
            ],
            [
                'title' => 'Featured listings active',
                'count' => $featuredInventory,
            ],
            [
                'title' => 'Articles published',
                'count' => $publishedArticles,
            ],
        ];

        // Analytics: status distribution
        $statusDistribution = Property::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(function ($row) {
                return [
                    'label' => ucfirst((string) $row->status),
                    'value' => (int) $row->total,
                ];
            })
            ->values();

        // Analytics: purpose distribution
        $purposeDistribution = Property::select('purpose', DB::raw('COUNT(*) as total'))
            ->groupBy('purpose')
            ->get()
            ->map(function ($row) {
                return [
                    'label' => ucfirst((string) $row->purpose),
                    'value' => (int) $row->total,
                ];
            })
            ->values();

        // Analytics: city-wise submissions
        $cityWiseSubmissions = DB::table('properties')
            ->leftJoin('cities', 'properties.city_id', '=', 'cities.id')
            ->selectRaw("IFNULL(cities.name, CONCAT('City #', properties.city_id)) as city, COUNT(properties.id) as total")
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                return [
                    'label' => $row->city,
                    'value' => (int) $row->total,
                ];
            })
            ->values();

        // Analytics: last 7 days submissions
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $rawTrend = Property::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $submissionTrend = collect(range(0, 6))->map(function ($i) use ($rawTrend) {
            $date = Carbon::now()->subDays(6 - $i)->format('Y-m-d');
            return [
                'label' => $date,
                'value' => isset($rawTrend[$date]) ? (int) $rawTrend[$date]->total : 0,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Admin dashboard stats fetched successfully',
            'data' => [
                'overview' => [
                    'total_properties' => $totalProperties,
                    'pending_approval' => $pendingApproval,
                    'registered_users' => $registeredUsers,
                    'published_articles' => $publishedArticles,
                ],
                'performance' => [
                    'approved_listings' => $approvedListings,
                    'rejected_flagged' => $rejectedFlagged,
                    'featured_inventory' => $featuredInventory,
                    'monthly_revenue' => (float) $monthlyRevenue,
                    'portal_views' => $portalViews,
                    'calls' => $calls,
                    'emails' => $emails,
                ],
                'priorities' => $priorities,
                'analytics' => [
                    'status_distribution' => $statusDistribution,
                    'purpose_distribution' => $purposeDistribution,
                    'city_wise_submissions' => $cityWiseSubmissions,
                    'submission_trend_last_7_days' => $submissionTrend,
                ],
            ],
        ]);
    }
}