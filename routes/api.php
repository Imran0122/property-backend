<?php
use App\Http\Controllers\API\HomeLoanController;
use App\Http\Middleware\AdminMiddleware;
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
use App\Http\Controllers\API\Admin\AdminDashboardController;
use App\Http\Controllers\API\Admin\RecentPropertiesController;
use App\Http\Controllers\API\Admin\ReportController;
use App\Http\Controllers\API\Admin\AdminUserController;
use App\Http\Controllers\API\Admin\AdminArticleController;
use App\Http\Controllers\API\Admin\AdminSettingsController;
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
use App\Http\Controllers\API\MostViewedSocietyController;
use App\Http\Controllers\API\SocietyMapController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\ToolsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ConstructionCalculatorController;
use App\Http\Controllers\API\ConstructionController;
use App\Http\Controllers\API\PropertyImageController;
use App\Http\Controllers\API\PropertyAmenityController;
use App\Http\Controllers\API\AmenityController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\PropertyBoutiqueController;
use App\Http\Controllers\API\Admin\AdminSocietyController;
use App\Http\Controllers\API\Admin\AdminSocietyMapController;
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


Route::get('/amenities', [AmenityController::class,'index']);
Route::get('/dashboard-stats', [DashboardController::class, 'index']);
Route::get('/home-loan/calculate', [HomeLoanController::class, 'calculate']);

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
// Route::get('/dashboard', [DashboardController::class, 'index']);
Route::post('/saved-searches', [SavedSearchController::class, 'store']);
Route::get('/saved-searches', [SavedSearchController::class, 'index']);



// Route::post('/properties', [PropertyController::class, 'store']);
// Route::post('/properties', [PropertyController::class, 'store']);


Route::get('admin/dashboard', [DashboardController::class, 'index']);
Route::post('payment/stripe/create-intent', [StripeController::class, 'createIntent']);
Route::post('payment/paypal/create', [PaypalController::class, 'create']);
Route::get('payment/paypal/success', [PaypalController::class, 'success']);
Route::get('payment/paypal/cancel', [PaypalController::class, 'cancel']);
Route::get('property/{slug}', [PropertyController::class, 'showBySlug']);
// Route::get('useful-links', [ToolsController::class, 'usefulLinks']);
// Route::get('/tools/useful-links', [ToolsController::class, 'usefulLinks']);
// Route::get('/home/{type}/{city}', [PropertyController::class, 'locationSearch']);

Route::post('/property-amenities', [PropertyAmenityController::class, 'store']);
Route::get('/property-amenities/{property_id}', [PropertyAmenityController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');




// Route::get('/home/projects', [HomeController::class, 'projects']);
Route::get('home/projects', [HomeController::class, 'projects']);
// Route::get('/popular-locations', [LocationController::class, 'popular']);
// Route::get('/popular-locations', [LocationController::class, 'popularLocations']);
Route::get('/area-guides',[AreaGuideController::class,'index']);
// Route::get('/most-viewed-societies', [AreaGuideController::class,'mostViewed']);
Route::get('/search-cities', [AreaGuideController::class,'searchCities']);
// Route::get('/most-viewed-societies', [SocietyController::class, 'mostViewedSocieties']);
Route::get('/most-viewed-societies', [MostViewedSocietyController::class, 'index']);
Route::get('/society-maps', [SocietyMapController::class, 'index']);
Route::get('/society-maps/city/{id}', [SocietyMapController::class, 'societiesByCity']);
Route::get('/society-maps/{slug}', [SocietyMapController::class, 'show']);
Route::get('/banks', [BankController::class, 'index']);
// Route::get('{type}/{city}', [PropertyController::class, 'locationSearch']);
Route::get('/useful-links', [ToolsController::class, 'usefulLinks']);

Route::post('/property-images', [PropertyImageController::class, 'store']);


Route::prefix('construction')->group(function () {

    // Dropdown APIs
    Route::get('/cities', [ConstructionController::class, 'cities']);
    Route::get('/types', [ConstructionController::class, 'types']);
    Route::get('/modes', [ConstructionController::class, 'modes']);

    // Main Calculator API
    Route::post('/calculate', [ConstructionCalculatorController::class, 'calculate']);
    // Route::post('/construction/calculate', [ConstructionController::class, 'calculate']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'user' => $request->user(),
    ]);
});

Route::get('/test', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is working'
    ]);
});

Route::get('/home/{type}/{city}', [PropertyController::class, 'locationSearch']);

Route::get('{type}/{city}', [PropertyController::class, 'locationSearch'])
    ->where([
        'type' => 'homes-for-sale|homes-for-rent|plots-for-sale|plots-for-rent|commercial-for-sale|commercial-for-rent',
        'city' => '[A-Za-z\-]+'
    ]);














// ---------------- Public APIs ---------------- //
Route::get('/properties', [PropertyController::class, 'index']); 
// Route::get('/dashboard/properties',[PropertyController::class,'dashboardProperties']);

// Route::get('/dashboard/properties/stats',[PropertyController::class,'dashboardPropertyStats']);  
       // List properties with filters


Route::get('/properties/listing', [PropertyController::class, 'listing']);
Route::get('/properties/listing/meta', [PropertyController::class, 'listingMeta']);       

Route::get('/properties/{id}', [PropertyController::class, 'show']);      // Show single property detail
Route::get('/cities', [CityController::class, 'index']);   
Route::get('/areas', [AreaController::class, 'index']); // ?city_id=1
               // List all cities

// // ---------------- Protected APIs ---------------- //
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/my-properties', [DashboardController::class, 'myProperties']);

    Route::get('/dashboard/properties', [PropertyController::class, 'dashboardProperties']);
    Route::get('/dashboard/properties/stats', [PropertyController::class, 'dashboardPropertyStats']);

    Route::get('/dashboard/post-listing/meta', [PropertyController::class, 'dashboardPostListingMeta']);
    Route::get('/dashboard/post-listing/{id}', [PropertyController::class, 'dashboardPostListingShow']);
    Route::post('/dashboard/post-listing', [PropertyController::class, 'store']);
    Route::post('/dashboard/post-listing/{id}', [PropertyController::class, 'update']);

    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

    Route::post('/properties/{id}/favorite', [PropertyController::class, 'toggleFavorite']);

    Route::post('/messages/send', [MessageController::class, 'send']);
    Route::get('/messages/conversations', [MessageController::class, 'conversations']);
    Route::get('/messages/conversation/{otherUserId}/{propertyId?}', [MessageController::class, 'conversation']);
    Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
});

// });
    // Property CRUD
    // Route::post('/properties', [PropertyController::class, 'store']);      // Add property
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


Route::middleware('auth:sanctum')->group(function () {

    // Inbox page APIs
    Route::get('/inbox', [MessageController::class, 'inbox']);
    Route::get('/inbox/trash', [MessageController::class, 'trash']);
    Route::get('/inbox/stats', [MessageController::class, 'stats']);
    Route::patch('/inbox/{id}/read', [MessageController::class, 'markAsRead']);
    Route::patch('/inbox/{id}/trash', [MessageController::class, 'moveToTrash']);
    Route::patch('/inbox/{id}/restore', [MessageController::class, 'restore']);

    // Existing messaging system
    Route::post('/messages/send', [MessageController::class, 'send']);
    Route::get('/messages/conversations', [MessageController::class, 'conversations']);
    Route::get('/messages/conversation/{otherUserId}/{propertyId?}', [MessageController::class, 'conversation']);
    Route::match(['post', 'patch'], '/messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/settings', [SettingsController::class, 'getSettings']);
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile']);
    Route::post('/settings/preferences', [SettingsController::class, 'updatePreferences']);
    Route::post('/settings/password', [SettingsController::class, 'changePassword']);
});

Route::middleware('auth:sanctum')->prefix('property-boutique')->group(function () {
    Route::get('/products', [PropertyBoutiqueController::class, 'products']);
    Route::get('/cart', [PropertyBoutiqueController::class, 'cart']);
    Route::post('/cart', [PropertyBoutiqueController::class, 'addToCart']);
    Route::patch('/cart/{id}', [PropertyBoutiqueController::class, 'updateCartItem']);
    Route::delete('/cart/{id}', [PropertyBoutiqueController::class, 'removeCartItem']);
    Route::delete('/cart', [PropertyBoutiqueController::class, 'clearCart']);

    Route::post('/checkout', [PropertyBoutiqueController::class, 'checkout']);

    Route::get('/orders', [PropertyBoutiqueController::class, 'orders']);
    Route::get('/orders/{id}', [PropertyBoutiqueController::class, 'orderShow']);
});



/*
|--------------------------------------------------------------------------
| Admin APIs (Cleaned - merged duplicate admin route groups only)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Admin APIs (Cleaned - merged duplicate admin route groups only)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {

    // Dashboard
    Route::get('dashboard/stats', [AdminDashboardController::class, 'stats']);

    // Property moderation
    // IMPORTANT: special routes pehle, {id} baad me
    Route::get('properties/recent', [RecentPropertiesController::class, 'index']);
    Route::get('properties/pending', [PropertyApprovalController::class, 'pending']);

    Route::get('properties/{id}', [PropertyApprovalController::class, 'show'])->whereNumber('id');
    Route::post('properties/{id}/approve', [PropertyApprovalController::class, 'approve'])->whereNumber('id');
    Route::post('properties/{id}/reject', [PropertyApprovalController::class, 'reject'])->whereNumber('id');
    Route::post('properties/{id}/status', [PropertyApprovalController::class, 'updateStatus'])->whereNumber('id');
    Route::post('properties/{id}/feature', [PropertyApprovalController::class, 'feature'])->whereNumber('id');
    Route::post('properties/{id}/unfeature', [PropertyApprovalController::class, 'unfeature'])->whereNumber('id');

    // Agent moderation
    Route::get('agents/pending', [AgentApprovalController::class, 'pending']);
    Route::post('agents/{id}/approve', [AgentApprovalController::class, 'approve'])->whereNumber('id');
    Route::post('agents/{id}/reject', [AgentApprovalController::class, 'reject'])->whereNumber('id');

    // Inquiries
    Route::get('inquiries', [InquiryController::class, 'index']);

    // Reports
    Route::get('report/properties', [ReportController::class, 'propertiesReport']);

    // Payments approval
    Route::post('invoice/{id}/approve', [PaymentApprovalController::class, 'approve'])->whereNumber('id');


    // Users & Agents Management
Route::get('users', [AdminUserController::class, 'index']);
Route::get('users/{id}', [AdminUserController::class, 'show'])->whereNumber('id');
Route::put('users/{id}', [AdminUserController::class, 'update'])->whereNumber('id');
Route::post('users/{id}/suspend', [AdminUserController::class, 'suspend'])->whereNumber('id');
Route::post('users/{id}/activate', [AdminUserController::class, 'activate'])->whereNumber('id');


// Articles & Blog Management
Route::get('articles', [AdminArticleController::class, 'index']);
Route::get('articles/meta', [AdminArticleController::class, 'meta']);
Route::get('articles/{id}', [AdminArticleController::class, 'show'])->whereNumber('id');
Route::post('articles', [AdminArticleController::class, 'store']);
Route::put('articles/{id}', [AdminArticleController::class, 'update'])->whereNumber('id');
Route::delete('articles/{id}', [AdminArticleController::class, 'destroy'])->whereNumber('id');
Route::post('articles/{id}/publish', [AdminArticleController::class, 'publish'])->whereNumber('id');
Route::post('articles/{id}/draft', [AdminArticleController::class, 'draft'])->whereNumber('id');


// settings
  Route::get('/settings', [AdminSettingsController::class, 'index']);
    Route::put('/settings', [AdminSettingsController::class, 'update']);


// Societies
Route::get('societies', [AdminSocietyController::class, 'index']);
Route::post('societies', [AdminSocietyController::class, 'store']);
Route::get('societies/{id}', [AdminSocietyController::class, 'show'])->whereNumber('id');
Route::put('societies/{id}', [AdminSocietyController::class, 'update'])->whereNumber('id');
Route::delete('societies/{id}', [AdminSocietyController::class, 'destroy'])->whereNumber('id');

// Society Maps
Route::get('society-maps', [AdminSocietyMapController::class, 'index']);
Route::post('society-maps', [AdminSocietyMapController::class, 'store']);
Route::get('society-maps/{id}', [AdminSocietyMapController::class, 'show'])->whereNumber('id');
Route::put('society-maps/{id}', [AdminSocietyMapController::class, 'update'])->whereNumber('id');
Route::delete('society-maps/{id}', [AdminSocietyMapController::class, 'destroy'])->whereNumber('id');

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('save-property', [SavedPropertyController::class, 'toggle']);
    Route::get('saved-properties', [SavedPropertyController::class, 'index']);
});