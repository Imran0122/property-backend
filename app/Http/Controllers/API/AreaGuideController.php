<?php

// namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\City;
// use App\Models\Area;
// use App\Models\Society;
// use Illuminate\Support\Str;

// class AreaGuideController extends Controller
// {
//     public function index()
//     {
//         $cities = City::select('id', 'name')->orderBy('name')->get();

//         $data = [];

//         foreach ($cities as $city) {
//             $popular = Society::with(['city:id,name', 'images'])
//                 ->where('city_id', $city->id)
//                 ->where('is_popular', 1)
//                 ->orderByDesc('views')
//                 ->orderBy('name')
//                 ->get()
//                 ->map(fn ($society) => $this->transformSocietyCard($society))
//                 ->values();

//             $links = Society::with(['city:id,name', 'images'])
//                 ->where('city_id', $city->id)
//                 ->orderByDesc('views')
//                 ->orderBy('name')
//                 ->get()
//                 ->map(fn ($society) => $this->transformSocietyCard($society))
//                 ->values();

//             if ($popular->isNotEmpty() || $links->isNotEmpty()) {
//                 $data[$city->name] = [
//                     'popular' => $popular,
//                     'links' => $links,
//                 ];
//             }
//         }

//         return response()->json($data);
//     }

//     public function show($slug)
//     {
//         $society = Society::with(['city:id,name', 'images'])
//             ->where('slug', $slug)
//             ->orWhere('id', $slug)
//             ->first();

//         if (!$society) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Area guide not found',
//             ], 404);
//         }

//         return response()->json([
//             'status' => true,
//             'data' => $this->transformSocietyDetail($society),
//         ]);
//     }

//     public function mostViewed()
//     {
//         $cities = City::all();
//         $data = [];

//         foreach ($cities as $city) {
//             $areas = Area::where('city_id', $city->id)
//                 ->orderBy('views', 'desc')
//                 ->take(6)
//                 ->get(['name', 'slug', 'views']);

//             if ($areas->count()) {
//                 $data[$city->name] = $areas;
//             }
//         }

//         return response()->json($data);
//     }

//     public function searchCities(Request $request)
//     {
//         $search = $request->search;

//         $cities = City::where('name', 'LIKE', "%{$search}%")
//             ->get(['id', 'name']);

//         return response()->json($cities);
//     }

//     private function transformSocietyCard(Society $society): array
//     {
//         $coverImage = $this->resolveSocietyCoverImage($society);

//         $mapImageModel = $this->pickImage($society, [
//             'society_map',
//             'society-map',
//             'map',
//             'master_plan',
//             'master-plan',
//             'plan',
//         ]);

//         $mapImage = $mapImageModel ? $this->resolveImageUrl($mapImageModel->image) : null;

//         return [
//             'id' => $society->id,
//             'slug' => $society->slug,
//             'name' => $society->name,
//             'city_name' => optional($society->city)->name,
//             'description' => $society->description,
//             'image' => $coverImage,
//             'image_url' => $coverImage,
//             'image_path' => $coverImage,
//             'map_image' => $mapImage,
//             'map_image_url' => $mapImage,
//             'views' => (int) ($society->views ?? 0),
//         ];
//     }

//     private function transformSocietyDetail(Society $society): array
//     {
//         $coverImage = $this->resolveSocietyCoverImage($society);

//         $mapImageModel = $this->pickImage($society, [
//             'society_map',
//             'society-map',
//             'map',
//             'master_plan',
//             'master-plan',
//             'plan',
//         ]);

//         $mapImage = $mapImageModel ? $this->resolveImageUrl($mapImageModel->image) : null;

//         $gallery = collect($society->images)
//             ->map(function ($image) {
//                 $resolved = $this->resolveImageUrl($image->image);

//                 return [
//                     'id' => $image->id,
//                     'image' => $resolved,
//                     'image_url' => $resolved,
//                     'type' => $image->type,
//                     'title' => $image->title,
//                     'sort_order' => (int) ($image->sort_order ?? 0),
//                 ];
//             })
//             ->filter(fn ($item) => !empty($item['image']))
//             ->values();

//         if ($gallery->isEmpty() && $coverImage) {
//             $gallery = collect([
//                 [
//                     'id' => null,
//                     'image' => $coverImage,
//                     'image_url' => $coverImage,
//                     'type' => 'cover',
//                     'title' => $society->name,
//                     'sort_order' => 0,
//                 ],
//             ]);
//         }

//         return [
//             'id' => $society->id,
//             'slug' => $society->slug,
//             'name' => $society->name,
//             'city_name' => optional($society->city)->name,
//             'description' => $society->description,
//             'society_image' => $coverImage,
//             'society_image_url' => $coverImage,
//             'image' => $coverImage,
//             'image_url' => $coverImage,
//             'image_path' => $coverImage,
//             'map_image' => $mapImage,
//             'map_image_url' => $mapImage,
//             'gallery' => $gallery,
//             'external_map_url' =>
//                 $society->plot_finder_url ??
//                 $society->map_url ??
//                 $society->google_map_url ??
//                 $society->location_url,
//         ];
//     }

//     private function resolveSocietyCoverImage(Society $society): ?string
//     {
//         if (!empty($society->image)) {
//             return $this->resolveImageUrl($society->image);
//         }

//         $coverImageModel = $this->pickImage($society, [
//             'cover',
//             'hero',
//             'main',
//             'featured',
//             'society',
//             'thumbnail',
//             'photo',
//         ]);

//         if ($coverImageModel && !empty($coverImageModel->image)) {
//             return $this->resolveImageUrl($coverImageModel->image);
//         }

//         $firstImage = collect($society->images)->sortBy('sort_order')->first();

//         return $firstImage ? $this->resolveImageUrl($firstImage->image) : null;
//     }

//     private function pickImage(Society $society, array $preferredTypes = [])
//     {
//         $images = collect($society->images);

//         foreach ($preferredTypes as $type) {
//             $match = $images->first(function ($img) use ($type) {
//                 return strtolower((string) $img->type) === strtolower($type);
//             });

//             if ($match) {
//                 return $match;
//             }
//         }

//         return $images->sortBy('sort_order')->first();
//     }

//     private function resolveImageUrl(?string $path): ?string
//     {
//         if (!$path) {
//             return null;
//         }

//         $path = trim(str_replace('\\', '/', $path));

//         if (Str::startsWith($path, ['http://', 'https://'])) {
//             return $path;
//         }

//         if (Str::startsWith($path, '/storage/')) {
//             return url(ltrim($path, '/'));
//         }

//         if (Str::startsWith($path, 'storage/')) {
//             return url($path);
//         }

//         if (Str::startsWith($path, 'public/')) {
//             return url('storage/' . ltrim(Str::after($path, 'public/'), '/'));
//         }

//         return url('storage/' . ltrim($path, '/'));
//     }
// }




namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Area;
use App\Models\Society;
use Illuminate\Support\Str;

class AreaGuideController extends Controller
{
    public function index()
    {
        $cities = City::select('id', 'name')->orderBy('name')->get();

        $data = [];

        foreach ($cities as $city) {
            $popular = Society::with(['city:id,name', 'images'])
                ->where('city_id', $city->id)
                ->where('is_popular', 1)
                ->orderByDesc('views')
                ->orderBy('name')
                ->get()
                ->map(fn ($society) => $this->transformSocietyCard($society))
                ->values();

            $links = Society::with(['city:id,name', 'images'])
                ->where('city_id', $city->id)
                ->orderByDesc('views')
                ->orderBy('name')
                ->get()
                ->map(fn ($society) => $this->transformSocietyCard($society))
                ->values();

            if ($popular->isNotEmpty() || $links->isNotEmpty()) {
                $data[$city->name] = [
                    'popular' => $popular,
                    'links'   => $links,
                ];
            }
        }

        return response()->json($data);
    }

    public function show($slug)
    {
        $society = Society::with(['city:id,name', 'images'])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$society) {
            return response()->json([
                'status'  => false,
                'message' => 'Area guide not found',
            ], 404);
        }

        $relatedSocieties = Society::with(['city:id,name', 'images'])
            ->where('city_id', $society->city_id)
            ->where('id', '!=', $society->id)
            ->orderByDesc('is_popular')
            ->orderByDesc('views')
            ->orderBy('name')
            ->take(8)
            ->get()
            ->map(fn ($item) => $this->transformSocietyCard($item))
            ->values();

        return response()->json([
            'status' => true,
            'data'   => [
                ...$this->transformSocietyDetail($society),
                'related_societies' => $relatedSocieties,
                'nearby_societies'  => $relatedSocieties->pluck('name')->values(),
            ],
        ]);
    }

    public function mostViewed()
    {
        $cities = City::all();
        $data = [];

        foreach ($cities as $city) {
            $areas = Area::where('city_id', $city->id)
                ->orderBy('views', 'desc')
                ->take(6)
                ->get(['name', 'slug', 'views']);

            if ($areas->count()) {
                $data[$city->name] = $areas;
            }
        }

        return response()->json($data);
    }

    public function searchCities(Request $request)
    {
        $search = $request->search;

        $cities = City::where('name', 'LIKE', "%{$search}%")
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    private function transformSocietyCard(Society $society): array
    {
        $coverImage = $this->resolveSocietyCoverImage($society);

        $mapImageModel = $this->pickImage($society, [
            'society_map',
            'society-map',
            'society map',
            'map',
            'master_plan',
            'master-plan',
            'plan',
        ]);

        $mapImage = $mapImageModel ? $this->resolveImageUrl($mapImageModel->image) : null;

        return [
            'id'            => $society->id,
            'slug'          => $society->slug,
            'name'          => $society->name,
            'city_name'     => optional($society->city)->name,
            'description'   => $society->description,
            'image'         => $coverImage,
            'image_url'     => $coverImage,
            'image_path'    => $coverImage,
            'society_image' => $coverImage,
            'map_image'     => $mapImage,
            'map_image_url' => $mapImage,
            'views'         => (int) ($society->views ?? 0),
        ];
    }

    private function transformSocietyDetail(Society $society): array
    {
        $coverImage = $this->resolveSocietyCoverImage($society);

        $mapImageModel = $this->pickImage($society, [
            'society_map',
            'society-map',
            'society map',
            'map',
            'master_plan',
            'master-plan',
            'plan',
        ]);

        $mapImage = $mapImageModel ? $this->resolveImageUrl($mapImageModel->image) : null;

        $gallery = $this->buildGallery($society, $coverImage);

        return [
            'id'               => $society->id,
            'slug'             => $society->slug,
            'name'             => $society->name,
            'city_name'        => optional($society->city)->name,
            'description'      => $society->description,
            'society_image'    => $coverImage,
            'society_image_url'=> $coverImage,
            'image'            => $coverImage,
            'image_url'        => $coverImage,
            'image_path'       => $coverImage,
            'map_image'        => $mapImage,
            'map_image_url'    => $mapImage,
            'gallery'          => $gallery,
            'external_map_url' =>
                $society->plot_finder_url ??
                $society->map_url ??
                $society->google_map_url ??
                $society->location_url,
            'house_sale_range' => $society->house_sale_range ?? null,
            'plot_sale_range'  => $society->plot_sale_range ?? null,
            'house_rent_range' => $society->house_rent_range ?? null,
        ];
    }

    private function buildGallery(Society $society, ?string $coverImage): array
    {
        $gallery = [];
        $seen = [];

        if ($coverImage) {
            $gallery[] = [
                'id'         => 'cover',
                'image'      => $coverImage,
                'image_url'  => $coverImage,
                'type'       => 'cover',
                'title'      => $society->name,
                'sort_order' => 0,
            ];
            $seen[] = $coverImage;
        }

        foreach ($society->images as $image) {
            $resolved = $this->resolveImageUrl($image->image);

            if (!$resolved || in_array($resolved, $seen, true)) {
                continue;
            }

            $gallery[] = [
                'id'         => $image->id,
                'image'      => $resolved,
                'image_url'  => $resolved,
                'type'       => $image->type,
                'title'      => $image->title ?: $society->name,
                'sort_order' => (int) ($image->sort_order ?? 0),
            ];

            $seen[] = $resolved;
        }

        return array_values($gallery);
    }

    private function resolveSocietyCoverImage(Society $society): ?string
    {
        if (!empty($society->image)) {
            return $this->resolveImageUrl($society->image);
        }

        $coverImageModel = $this->pickImage($society, [
            'cover',
            'hero',
            'main',
            'featured',
            'society',
            'thumbnail',
            'photo',
        ]);

        if ($coverImageModel && !empty($coverImageModel->image)) {
            return $this->resolveImageUrl($coverImageModel->image);
        }

        $firstImage = collect($society->images)->sortBy('sort_order')->first();

        return $firstImage ? $this->resolveImageUrl($firstImage->image) : null;
    }

    private function pickImage(Society $society, array $preferredTypes = [])
    {
        $images = collect($society->images);

        foreach ($preferredTypes as $type) {
            $match = $images->first(function ($img) use ($type) {
                return strtolower((string) $img->type) === strtolower($type);
            });

            if ($match) {
                return $match;
            }
        }

        return $images->sortBy('sort_order')->first();
    }

    private function resolveImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = trim(str_replace('\\', '/', $path));

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/storage/')) {
            return url(ltrim($path, '/'));
        }

        if (Str::startsWith($path, 'storage/')) {
            return url($path);
        }

        if (Str::startsWith($path, 'public/')) {
            return url('storage/' . ltrim(Str::after($path, 'public/'), '/'));
        }

        return url('storage/' . ltrim($path, '/'));
    }
}