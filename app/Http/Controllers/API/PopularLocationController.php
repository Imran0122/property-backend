<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PopularLocationController extends Controller
{
    public function index()
    {
        $purposes = ['sale', 'rent'];
        $response = [];

        foreach ($purposes as $purpose) {
            $rows = Property::query()
                ->select(
                    'cities.name as city',
                    'areas.name as location',
                    'property_types.name as property_type',
                    DB::raw('COUNT(properties.id) as total')
                )
                ->join('cities', 'cities.id', '=', 'properties.city_id')
                ->join('areas', 'areas.id', '=', 'properties.area_id')
                ->join('property_types', 'property_types.id', '=', 'properties.property_type_id')
                ->whereIn('properties.status', ['active', 'published'])
                ->where('properties.purpose', $purpose)
                ->groupBy('cities.name', 'areas.name', 'property_types.name')
                ->orderByDesc('total')
                ->get();

            $grouped = [
                'plots' => [],
                'flats' => [],
                'houses' => [],
            ];

            foreach ($rows as $row) {
                $bucket = $this->resolvePopularBucket($row->property_type);

                if (!$bucket) {
                    continue;
                }

                $cityName = trim((string) $row->city);
                $locationName = trim((string) $row->location);

                if ($cityName === '' || $locationName === '') {
                    continue;
                }

                if (!isset($grouped[$bucket][$cityName])) {
                    $grouped[$bucket][$cityName] = [];
                }

                // har city me max 8 locations
                if (count($grouped[$bucket][$cityName]) >= 8) {
                    continue;
                }

                $grouped[$bucket][$cityName][] = [
                    'city' => $cityName,
                    'location' => $locationName,
                    'total' => (int) $row->total,
                ];
            }

            // top cities first
            foreach ($grouped as $bucket => $cities) {
                $grouped[$bucket] = collect($cities)
                    ->sortByDesc(fn ($areas) => collect($areas)->sum('total'))
                    ->take(4)
                    ->map(fn ($areas) => array_values($areas))
                    ->toArray();
            }

            $response[$purpose] = array_filter(
                $grouped,
                fn ($cities) => !empty($cities)
            );
        }

        return response()->json($response);
    }

    private function resolvePopularBucket(?string $typeName): ?string
    {
        $typeName = Str::lower(trim((string) $typeName));

        if ($typeName === '') {
            return null;
        }

        $plotKeywords = [
            'plot',
            'plots',
            'residential plot',
            'commercial plot',
            'agricultural land',
            'industrial land',
            'land',
        ];

        $flatKeywords = [
            'apartment',
            'flat',
            'studio',
            'penthouse',
            'room',
        ];

        $houseKeywords = [
            'house',
            'houses',
            'home',
            'homes',
            'villa',
            'farm house',
        ];

        if (
            in_array($typeName, $plotKeywords, true) ||
            str_contains($typeName, 'plot') ||
            str_contains($typeName, 'land')
        ) {
            return 'plots';
        }

        if (
            in_array($typeName, $flatKeywords, true) ||
            str_contains($typeName, 'apartment') ||
            str_contains($typeName, 'flat') ||
            str_contains($typeName, 'studio') ||
            str_contains($typeName, 'penthouse') ||
            str_contains($typeName, 'room')
        ) {
            return 'flats';
        }

        if (
            in_array($typeName, $houseKeywords, true) ||
            str_contains($typeName, 'house') ||
            str_contains($typeName, 'home') ||
            str_contains($typeName, 'villa')
        ) {
            return 'houses';
        }

        return null;
    }
}