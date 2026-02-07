<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        /**
         * ===============================
         * FEATURED PROPERTIES (OLD CODE)
         * ===============================
         */
        $featuredProperties = Property::with(['city', 'images'])
            ->where('status', 'active')
            ->take(6)
            ->get();


        /**
         * ===============================
         * HOME PAGE - PROJET HECTARE SECTION
         * ===============================
         */
        $featuredProjects = DB::select("
            SELECT 
                p.id,
                p.title,
                p.slug,
                p.location,
                p.cover_image,

                MIN(u.price) AS min_price,
                MAX(u.price) AS max_price,

                MIN(u.area) AS min_area,
                MAX(u.area) AS max_area,

                GROUP_CONCAT(DISTINCT u.type SEPARATOR ', ') AS unit_types

            FROM projects p
            JOIN project_units u ON u.project_id = p.id
            WHERE p.status = 1
            AND p.is_featured = 1
            GROUP BY p.id
            ORDER BY p.created_at DESC
            LIMIT 6
        ");


        /**
         * ===============================
         * SEND DATA TO HOME VIEW
         * ===============================
         */
        return view('home', [
            'featuredProperties' => $featuredProperties,
            'featuredProjects'   => $featuredProjects,
        ]);
    }
}
