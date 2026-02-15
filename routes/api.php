<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PropertyController;
use App\Http\Controllers\API\CityController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ProjectController;
use Illuminate\Http\Request;
use App\Http\Controllers\API\AreaController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\BrowsePropertiesController;
use App\Http\Controllers\API\AgencyController;
use App\Http\Controllers\API\PopularLocationController;
use App\Http\Controllers\API\BrowseCityController;
use App\Http\Controllers\API\ViewedPropertyController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\InquiryController;
use App\Http\Controllers\API\AgentController;
use App\Http\Controllers\API\FavouriteController;
use App\Http\Controllers\API\Admin\AdminController;
use App\Http\Controllers\API\Admin\AdminPropertyController;
use App\Http\Controllers\API\Admin\AdminAgentController;
use App\Http\Controllers\API\Admin\PropertyApprovalController;
use App\Http\Controllers\API\Admin\AgentApprovalController;
use App\Http\Controllers\API\HomeApiController;
use App\Http\Controllers\API\SavedSearchController;
use App\Http\Controllers\API\SavedPropertyController;
use App\Http\Controllers\API\StripeController;
use App\Http\Controllers\API\PaypalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\FeaturePropertyController;
use App\Http\Controllers\API\PaymentApprovalController;
use App\Http\Controllers\API\AreaGuideController;
use App\Http\Controllers\SocietyController;


// use App\Http\Controllers\API\LocationController;

// use App\Http\Controllers\Api\HomeController;


// use App\Http\Controllers\API\Admin\InquiryController;



// routes/api.php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All API endpoints for the property website (Zameen.com clone)
|--------------------------------------------------------------------------
*/
Route::get('/properties/search', [PropertyController::class, 'searchApi']);
Route::get('/properties/filter', [PropertyController::class, 'filter']);
Route::get('/projects', [ProjectController::class, 'index']);

Route::get('/property-types', [PropertyController::class, 'getTypes']);
Route::get('/property-subtypes/{type_id}', [PropertyController::class, 'getSubTypes']);
Route::get('/price-range', [PropertyController::class, 'priceRange']);
Route::get('/area-range', [PropertyController::class, 'areaRange']);
Route::get('/beds', [PropertyController::class, 'beds']);
Route::get('/search-location', [SearchController::class, 'search']);

Route::get('/cities/{city}/areas', [AreaController::class, 'getAreasByCity']);
Route::get('/browse-properties', [BrowsePropertiesController::class, 'index']);

Route::get('/titanium-agencies', [AgencyController::class, 'titaniumAgencies']);
Route::get('/agencies/{id}', [AgencyController::class, 'show']);
Route::get('/popular-locations', [PopularLocationController::class, 'index']);
Route::get('/browse-cities', [BrowseCityController::class, 'index']);
Route::get('/viewed-properties', [ViewedPropertyController::class, 'index']);
Route::get('/projects/trending', [ProjectController::class, 'trending']);
Route::get('/home/blogs', [HomeController::class, 'homeBlogs']);

Route::get('/community/blogs', [BlogController::class, 'index']);
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{slug}', [BlogController::class, 'show']);
Route::post('/property-inquiry', [InquiryController::class, 'store']);
Route::get('/agents/{id}', [AgentController::class, 'show']);
Route::get('/agents/{id}/properties', [AgentController::class, 'properties']);
Route::get('/agents', [AgentController::class, 'index']);
Route::get('/agencies', [AgencyController::class, 'index']);
// Route::get('/agencies/{id}', [AgencyController::class, 'show']);
Route::get('/seo/properties', [PropertyController::class, 'seoListing']);
Route::post('/property-inquiry', [InquiryController::class, 'store']);
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::post('/saved-searches', [SavedSearchController::class, 'store']);
Route::get('/saved-searches', [SavedSearchController::class, 'index']);
Route::post('/properties', [PropertyController::class, 'store']);
Route::get('admin/dashboard', [DashboardController::class, 'index']);
Route::post('payment/stripe/create-intent', [StripeController::class, 'createIntent']);
Route::post('payment/paypal/create', [PaypalController::class, 'create']);
Route::get('payment/paypal/success', [PaypalController::class, 'success']);
Route::get('payment/paypal/cancel', [PaypalController::class, 'cancel']);
Route::get('property/{slug}', [PropertyController::class, 'showBySlug']);
Route::get('/home/{type}/{city}', [PropertyController::class, 'locationSearch']);

Route::get('{type}/{city}', [PropertyController::class, 'locationSearch']);
// Route::get('/home/projects', [HomeController::class, 'projects']);
Route::get('home/projects', [HomeController::class, 'projects']);
// Route::get('/popular-locations', [LocationController::class, 'popular']);
// Route::get('/popular-locations', [LocationController::class, 'popularLocations']);
Route::get('/area-guides',[AreaGuideController::class,'index']);
// Route::get('/most-viewed-societies', [AreaGuideController::class,'mostViewed']);
Route::get('/search-cities', [AreaGuideController::class,'searchCities']);
Route::get('/most-viewed-societies', [SocietyController::class, 'mostViewedSocieties']);











// ---------------- Public APIs ---------------- //
Route::get('/properties', [PropertyController::class, 'index']);          // List properties with filters
Route::get('/properties/{id}', [PropertyController::class, 'show']);      // Show single property detail
Route::get('/cities', [CityController::class, 'index']);   
Route::get('/areas', [AreaController::class, 'index']); // ?city_id=1
               // List all cities

// // ---------------- Protected APIs ---------------- //
Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/my-properties', [DashboardController::class, 'myProperties']);

    // Property CRUD
    Route::post('/properties', [PropertyController::class, 'store']);      // Add property
    Route::put('/properties/{id}', [PropertyController::class, 'update']); // Update property
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']); // Delete property

    // Favorites
    Route::post('/properties/{id}/favorite', [PropertyController::class, 'toggleFavorite']);

    // Messaging system
    Route::post('/messages/send', [MessageController::class, 'send']);
    Route::get('/messages/conversations', [MessageController::class, 'conversations']);
    Route::get('/messages/conversation/{otherUserId}/{propertyId?}', [MessageController::class, 'conversation']);
    Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/favourites', [FavouriteController::class, 'store']);
    Route::delete('/favourites/{property_id}', [FavouriteController::class, 'destroy']);
    Route::get('/my-favourites', [FavouriteController::class, 'myFavourites']);
    
});

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/favorites', [FavoriteController::class, 'store']);
//     Route::get('/favorites', [FavoriteController::class, 'index']);
//     Route::delete('/favorites/{property_id}', [FavoriteController::class, 'destroy']);
// });


Route::prefix('admin')->group(function () {

    // Properties
    Route::get('/properties/pending', [AdminController::class, 'pendingProperties']);
    Route::post('/properties/{id}/approve', [AdminController::class, 'approveProperty']);
    Route::post('/properties/{id}/reject', [AdminController::class, 'rejectProperty']);

    Route::post('/properties/{id}/feature', [AdminController::class, 'featureProperty']);
    Route::post('/properties/{id}/unfeature', [AdminController::class, 'unfeatureProperty']);

    // Agents
    Route::get('/agents/pending', [AdminController::class, 'pendingAgents']);
    Route::post('/agents/{id}/approve', [AdminController::class, 'approveAgent']);
    Route::post('/agents/{id}/reject', [AdminController::class, 'rejectAgent']);
});

Route::prefix('admin')->group(function () {
    Route::post('properties/{id}/feature', [
        \App\Http\Controllers\API\Admin\PropertyApprovalController::class,
        'feature'
    ]);
});



Route::prefix('admin')->group(function () {

    // Properties
    Route::get('/properties/pending', [AdminPropertyController::class, 'pending']);
    Route::post('/properties/{id}/approve', [AdminPropertyController::class, 'approve']);
    Route::post('/properties/{id}/reject', [AdminPropertyController::class, 'reject']);
    Route::post('/properties/{id}/feature', [AdminPropertyController::class, 'feature']);

    // Agents
    Route::get('/agents/pending', [AdminAgentController::class, 'pending']);
    Route::post('/agents/{id}/approve', [AdminAgentController::class, 'approve']);
    Route::post('/agents/{id}/reject', [AdminAgentController::class, 'reject']);
});


Route::prefix('admin')->group(function () {

    // 🔹 Property approval
    Route::get('properties/pending', [PropertyApprovalController::class, 'pending']);
    Route::post('properties/{id}/approve', [PropertyApprovalController::class, 'approve']);
    Route::post('properties/{id}/reject', [PropertyApprovalController::class, 'reject']);

    // 🔹 Agent approval
    Route::get('agents/pending', [AgentApprovalController::class, 'pending']);
    Route::post('agents/{id}/approve', [AgentApprovalController::class, 'approve']);
    Route::post('agents/{id}/reject', [AgentApprovalController::class, 'reject']);

    // 🔹 Inquiries
    Route::get('inquiries', [InquiryController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // Property moderation
    Route::post('/properties/{id}/approve', [PropertyApprovalController::class, 'approve']);
    Route::post('/properties/{id}/reject', [PropertyApprovalController::class, 'reject']);
    Route::post('/properties/{id}/feature', [PropertyApprovalController::class, 'feature']);

    // Agent moderation
    Route::post('/agents/{id}/approve', [AgentApprovalController::class, 'approve']);
    Route::post('/agents/{id}/reject', [AgentApprovalController::class, 'reject']);

    // Inquiries
    Route::get('/inquiries', [InquiryController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function () {

    // Packages
    Route::get('/packages', [PackageController::class, 'index']);

    // Invoice
    Route::post('/invoice/create', [InvoiceController::class, 'create']);

    // Feature property
    Route::post('/property/feature', [FeaturePropertyController::class, 'feature']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // Payment approval
    Route::post('/admin/invoice/{id}/approve', [PaymentApprovalController::class, 'approve']);
});
Route::prefix('admin')->group(function () {
    Route::post('properties/{id}/status', [PropertyApprovalController::class, 'updateStatus']);
    Route::post('properties/{id}/feature', [PropertyApprovalController::class, 'feature']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('save-property', [SavedPropertyController::class, 'toggle']);
    Route::get('saved-properties', [SavedPropertyController::class, 'index']);
});

});



