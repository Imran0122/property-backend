<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BrowsePropertiesController extends Controller
{
    public function index()
    {
        // Get main property type IDs
        $types = DB::table('property_types')->pluck('id', 'name');

        return response()->json([
            'homes'       => $this->homes($types['Homes'] ?? null),
            'plots'       => $this->plots($types['Plots'] ?? null),
            'commercial'  => $this->commercial($types['Commercial'] ?? null),
        ]);
    }

    /* =========================
       HOMES
    ==========================*/
    private function homes($typeId)
    {
        if (!$typeId) return [];

        return [
            'popular' => DB::table('properties')
                ->join('property_features', 'properties.id', '=', 'property_features.property_id')
                ->where('properties.property_type_id', $typeId)
                ->where('properties.status', 'active')
                ->select('property_features.area', DB::raw('COUNT(*) as total'))
                ->groupBy('property_features.area')
                ->orderByDesc('total')
                ->limit(6)
                ->get(),

            'area_size' => DB::table('properties')
                ->join('property_features', 'properties.id', '=', 'property_features.property_id')
                ->where('properties.property_type_id', $typeId)
                ->where('properties.status', 'active')
                ->select('property_features.area')
                ->distinct()
                ->orderBy('property_features.area')
                ->get(),
        ];
    }

    /* =========================
       PLOTS
    ==========================*/
    private function plots($typeId)
    {
        if (!$typeId) return [];

        // Static types (zameen.com bhi aisa hi karta hai)
        return [
            'type' => [
                'Residential Plot',
                'Commercial Plot',
                'Plot File',
                'Plot Form',
                'Agricultural Land',
                'Industrial Land',
            ]
        ];
    }

    /* =========================
       COMMERCIAL
    ==========================*/
    private function commercial($typeId)
    {
        if (!$typeId) return [];

        return [
            'type' => [
                'Office',
                'Shop',
                'Building',
                'Warehouse',
                'Factory',
                'Other',
            ]
        ];
    }
}
