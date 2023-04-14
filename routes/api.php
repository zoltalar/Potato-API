<?php

// --------------------------------------------------
// Admin
// --------------------------------------------------

use App\Http\Controllers\Api\Admin\AdminController as ApiAdminController;
use App\Http\Controllers\Api\Admin\AuthenticationController as ApiAdminAuthenticationController;
use App\Http\Controllers\Api\Admin\CategoryController as ApiAdminCategoryController;
use App\Http\Controllers\Api\Admin\CityController as ApiAdminCityController;
use App\Http\Controllers\Api\Admin\CommentController as ApiAdminCommentController;
use App\Http\Controllers\Api\Admin\CountryController as ApiAdminCountryController;
use App\Http\Controllers\Api\Admin\EventController as ApiAdminEventController;
use App\Http\Controllers\Api\Admin\FarmController as ApiAdminFarmController;
use App\Http\Controllers\Api\Admin\ImageController as ApiAdminImageController;
use App\Http\Controllers\Api\Admin\InventoryController as ApiAdminInventoryController;
use App\Http\Controllers\Api\Admin\LanguageController as ApiAdminLanguageController;
use App\Http\Controllers\Api\Admin\MarketController as ApiAdminMarketController;
use App\Http\Controllers\Api\Admin\MessageController as ApiAdminMessageController;
use App\Http\Controllers\Api\Admin\ReviewController as ApiAdminReviewController;
use App\Http\Controllers\Api\Admin\TranslationController as ApiAdminTranslationController;
use App\Http\Controllers\Api\Admin\UserController as ApiAdminUserController;

// --------------------------------------------------
// Potato
// --------------------------------------------------

use App\Http\Controllers\Api\Potato\AccountController as ApiPotatoAccountController;
use App\Http\Controllers\Api\Potato\AddressController as ApiPotatoAddressController;
use App\Http\Controllers\Api\Potato\AuthenticationController as ApiPotatoAuthenticationController;
use App\Http\Controllers\Api\Potato\CategoryController as ApiPotatoCategoryController;
use App\Http\Controllers\Api\Potato\CityController as ApiPotatoCityController;
use App\Http\Controllers\Api\Potato\CommentController as ApiPotatoCommentController;
use App\Http\Controllers\Api\Potato\ContactController as ApiPotatoContactController;
use App\Http\Controllers\Api\Potato\CountryController as ApiPotatoCountryController;
use App\Http\Controllers\Api\Potato\CurrencyController as ApiPotatoCurrencyController;
use App\Http\Controllers\Api\Potato\EventController as ApiPotatoEventController;
use App\Http\Controllers\Api\Potato\FarmController as ApiPotatoFarmController;
use App\Http\Controllers\Api\Potato\FavoriteController as ApiPotatoFavoriteController;
use App\Http\Controllers\Api\Potato\ImageController as ApiPotatoImageController;
use App\Http\Controllers\Api\Potato\InventoryController as ApiPotatoInventoryController;
use App\Http\Controllers\Api\Potato\LanguageController as ApiPotatoLanguageController;
use App\Http\Controllers\Api\Potato\MarketController as ApiPotatoMarketController;
use App\Http\Controllers\Api\Potato\MessageController as ApiPotatoMessageController;
use App\Http\Controllers\Api\Potato\OperatingHourController as ApiPotatoOperatingHourController;
use App\Http\Controllers\Api\Potato\PriceController as ApiPotatoPriceController;
use App\Http\Controllers\Api\Potato\ProductController as ApiPotatoProductController;
use App\Http\Controllers\Api\Potato\RegisterController as ApiPotatoRegisterController;
use App\Http\Controllers\Api\Potato\ResetPasswordController as ApiPotatoResetPasswordController;
use App\Http\Controllers\Api\Potato\ReviewController as ApiPotatoReviewController;
use App\Http\Controllers\Api\Potato\UnitController as ApiPotatoUnitController;
use App\Http\Controllers\Api\Potato\UserController as ApiPotatoUserController;
use App\Http\Controllers\Api\Potato\VerificationController as ApiPotatoVerificationController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Admin routes
Route::prefix('7pyn5wd')->group(function() {

    // Authentication
    Route::prefix('authentication')->group(function () {
        Route::post('login', [ApiAdminAuthenticationController::class, 'login'])->name('api.admin.authentication.login');
        Route::post('logout', [ApiAdminAuthenticationController::class, 'logout'])->name('api.admin.authentication.logout');
    });

    // Admins
    Route::prefix('admins')->group(function() {
        Route::get('index', [ApiAdminController::class, 'index'])->name('api.admin.admins.index');
        Route::post('store', [ApiAdminController::class, 'store'])->name('api.admin.admins.store');
        Route::match(['PUT', 'PATCH'], 'update/{admin}', [ApiAdminController::class, 'update'])->name('api.admin.admins.update');
        Route::delete('{admin}', [ApiAdminController::class, 'destroy'])->name('api.admin.admins.destroy');
        Route::get('current', [ApiAdminController::class, 'current'])->name('api.admin.admins.current');
    });

    // Categories
    Route::prefix('categories')->group(function() {
        Route::get('index', [ApiAdminCategoryController::class, 'index'])->name('api.admin.categories.index');
        Route::post('store', [ApiAdminCategoryController::class, 'store'])->name('api.admin.categories.store');
        Route::match(['PUT', 'PATCH'], 'update/{category}', [ApiAdminCategoryController::class, 'update'])->name('api.admin.categories.update');
        Route::delete('{category}', [ApiAdminCategoryController::class, 'destroy'])->name('api.admin.categories.destroy');
        Route::get('meta', [ApiAdminCategoryController::class, 'meta'])->name('api.admin.categories.meta');
    });

    // Cities
    Route::prefix('cities')->group(function() {
        Route::get('index', [ApiAdminCityController::class, 'index'])->name('api.admin.cities.index');
        Route::post('store', [ApiAdminCityController::class, 'store'])->name('api.admin.cities.store');
        Route::match(['PUT', 'PATCH'], 'update/{city}', [ApiAdminCityController::class, 'update'])->name('api.admin.cities.update');
        Route::get('meta', [ApiAdminCityController::class, 'meta'])->name('api.admin.cities.meta');
    });

    // Comments
    Route::prefix('comments')->group(function() {
        Route::get('index', [ApiAdminCommentController::class, 'index'])->name('api.admin.comments.index');
        Route::delete('{comment}', [ApiAdminCommentController::class, 'destroy'])->name('api.admin.comments.destroy');
    });

    // Countries
    Route::prefix('countries')->group(function() {
        Route::get('index', [ApiAdminCountryController::class, 'index'])->name('api.admin.countries.index');
    });

    // Events
    Route::prefix('events')->group(function() {
        Route::get('index', [ApiAdminEventController::class, 'index'])->name('api.admin.events.index');
        Route::match(['PUT', 'PATCH'], 'update/{event}', [ApiAdminEventController::class, 'update'])->name('api.admin.events.update');
        Route::get('meta', [ApiAdminEventController::class, 'meta'])->name('api.admin.events.meta');
    });

    // Farms
    Route::prefix('farms')->group(function() {
       Route::get('index', [ApiAdminFarmController::class, 'index'])->name('api.admin.farms.index');
       Route::match(['PUT', 'PATCH'], 'update/{farm}', [ApiAdminFarmController::class, 'update'])->name('api.admin.farms.update');
       Route::match(['PUT', 'PATCH'], 'activate/{farm}', [ApiAdminFarmController::class, 'activate'])->name('api.admin.farms.activate');
    });

    // Images
    Route::prefix('images')->group(function() {
        Route::get('index', [ApiAdminImageController::class, 'index'])->name('api.admin.images.index');
        Route::delete('{image}', [ApiAdminImageController::class, 'destroy'])->name('api.admin.images.destroy');
    });

    // Inventory
    Route::prefix('inventory')->group(function() {
        Route::get('index', [ApiAdminInventoryController::class, 'index'])->name('api.admin.inventory.index');
        Route::post('store', [ApiAdminInventoryController::class, 'store'])->name('api.admin.inventory.store');
        Route::match(['PUT', 'PATCH'], 'update/{inventory}', [ApiAdminInventoryController::class, 'update'])->name('api.admin.inventory.update');
        Route::delete('{inventory}', [ApiAdminInventoryController::class, 'destroy'])->name('api.admin.inventory.destroy');
    });

    // Languages
    Route::prefix('languages')->group(function() {
        Route::get('index', [ApiAdminLanguageController::class, 'index'])->name('api.admin.languages.index');
        Route::post('store', [ApiAdminLanguageController::class, 'store'])->name('api.admin.languages.store');
        Route::match(['PUT', 'PATCH'], 'update/{language}', [ApiAdminLanguageController::class, 'update'])->name('api.admin.languages.update');
        Route::delete('{language}', [ApiAdminLanguageController::class, 'destroy'])->name('api.admin.languages.destroy');
    });

    // Markets
    Route::prefix('markets')->group(function() {
        Route::get('index', [ApiAdminMarketController::class, 'index'])->name('api.admin.markets.index');
        Route::match(['PUT', 'PATCH'], 'update/{market}', [ApiAdminMarketController::class, 'update'])->name('api.admin.markets.update');
        Route::match(['PUT', 'PATCH'], 'activate/{market}', [ApiAdminMarketController::class, 'activate'])->name('api.admin.markets.activate');
    });

    // Messages
    Route::prefix('messages')->group(function() {
        Route::get('index', [ApiAdminMessageController::class, 'index'])->name('api.admin.messages.index');
    });

    // Reviews
    Route::prefix('reviews')->group(function() {
        Route::get('index', [ApiAdminReviewController::class, 'index'])->name('api.admin.reviews.index');
        Route::match(['PUT', 'PATCH'], 'activate/{review}', [ApiAdminReviewController::class, 'activate'])->name('api.admin.reviews.activate');
        Route::match(['PUT', 'PATCH'], 'deactivate/{review}', [ApiAdminReviewController::class, 'deactivate'])->name('api.admin.reviews.deactivate');
    });

    // Translations
    Route::prefix('translations')->group(function() {
        Route::get('index', [ApiAdminTranslationController::class, 'index'])->name('api.admin.translations.index');
        Route::post('store', [ApiAdminTranslationController::class, 'store'])->name('api.admin.translations.store');
        Route::match(['PUT', 'PATCH'], 'update/{translation}', [ApiAdminTranslationController::class, 'update'])->name('api.admin.translations.update');
        Route::delete('{translation}', [ApiAdminTranslationController::class, 'destroy'])->name('api.admin.translations.destroy');
        Route::get('meta', [ApiAdminTranslationController::class, 'meta'])->name('api.admin.translations.meta');
    });

    // Users
    Route::prefix('users')->group(function() {
        Route::get('index', [ApiAdminUserController::class, 'index'])->name('api.admin.users.index');
        Route::post('store', [ApiAdminUserController::class, 'store'])->name('api.admin.users.store');
        Route::match(['PUT', 'PATCH'], 'update/{user}', [ApiAdminUserController::class, 'update'])->name('api.admin.users.update');
        Route::delete('{user}', [ApiAdminUserController::class, 'destroy'])->name('api.admin.users.destroy');
    });

});

// Potato routes
Route::prefix('potato')->group(function() {

    // Account
    Route::prefix('account')->group(function() {
        Route::get('events', [ApiPotatoAccountController::class, 'events'])->name('api.potato.account.events');
        Route::get('farms', [ApiPotatoAccountController::class, 'farms'])->name('api.potato.account.farms');
        Route::get('favorites', [ApiPotatoAccountController::class, 'favorites'])->name('api.potato.account.favorites');
        Route::get('markets', [ApiPotatoAccountController::class, 'markets'])->name('api.potato.account.markets');
        Route::get('messages', [ApiPotatoAccountController::class, 'messages'])->name('api.potato.account.messages');
        Route::get('reviews', [ApiPotatoAccountController::class, 'reviews'])->name('api.potato.account.reviews');
    });

    // Addresses
    Route::prefix('addresses')->group(function() {
        Route::post('save/{type}/{id}', [ApiPotatoAddressController::class, 'save'])->name('api.potato.addresses.save');
        Route::get('plot', [ApiPotatoAddressController::class, 'plot'])->name('api.potato.addresses.plot');
        Route::delete('{id}/{type}/{addressableId}', [ApiPotatoAddressController::class, 'destroy'])->name('api.potato.addresses.destroy');
        Route::get('meta', [ApiPotatoAddressController::class, 'meta'])->name('api.potato.addresses.meta');
    });

    // Authentication
    Route::prefix('authentication')->group(function() {
        Route::post('login', [ApiPotatoAuthenticationController::class, 'login'])->name('api.potato.authentication.login');
        Route::post('logout', [ApiPotatoAuthenticationController::class, 'logout'])->name('api.potato.authentication.logout');
    });

    // Categories
    Route::prefix('categories')->group(function() {
        Route::get('index', [ApiPotatoCategoryController::class, 'index'])->name('api.potato.categories.index');
    });

    // Cities
    Route::prefix('cities')->group(function() {
        Route::get('index', [ApiPotatoCityController::class, 'index'])->name('api.potato.cities.index');
        Route::get('show/{city}', [ApiPotatoCityController::class, 'show'])->name('api.potato.cities.show');
        Route::get('locate/{latitude}/{longitude}', [ApiPotatoCityController::class, 'locate'])->name('api.potato.cities.locate');
    });

    // Comments
    Route::prefix('comments')->group(function() {
        Route::post('store/{type}/{id}', [ApiPotatoCommentController::class, 'store'])->name('api.potato.comments.store');
    });

    // Contact
    Route::post('contact', [ApiPotatoContactController::class, 'contact'])->name('api.potato.contact');

    // Countries
    Route::prefix('countries')->group(function() {
        Route::get('index', [ApiPotatoCountryController::class, 'index'])->name('api.potato.countries.index');
    });

    // Currencies
    Route::prefix('currencies')->group(function() {
        Route::get('index', [ApiPotatoCurrencyController::class, 'index'])->name('api.potato.currencies.index');
    });

    // Email Verification
    Route::prefix('email')->group(function() {
        Route::match(['PUT', 'PATCH'], 'verify/{code}', [ApiPotatoVerificationController::class, 'verify'])->name('verification.verify');
        Route::post('resend', [ApiPotatoVerificationController::class, 'resend'])->name('verification.resend');
    });

    // Events
    Route::prefix('events')->group(function() {
        Route::get('index', [ApiPotatoEventController::class, 'index'])->name('api.potato.events.index');
        Route::post('store', [ApiPotatoEventController::class, 'store'])->name('api.potato.events.store');
        Route::get('show/{id}', [ApiPotatoEventController::class, 'show'])->name('api.potato.events.show');
        Route::get('locate/{latitude}/{longitude}', [ApiPotatoEventController::class, 'locate'])->name('api.potato.events.locate');
        Route::get('search', [ApiPotatoEventController::class, 'search'])->name('api.potato.events.search');
        Route::match(['PUT', 'PATCH'], 'update-general-information/{id}', [ApiPotatoEventController::class, 'updateGeneralInformation'])->name('api.potato.events.update-general-information');
        Route::match(['PUT', 'PATCH'], 'update-description/{id}', [ApiPotatoEventController::class, 'updateDescription'])->name('api.potato.events.update-description');
        Route::delete('{id}', [ApiPotatoEventController::class, 'destroy'])->name('api.potato.events.destroy');
        Route::get('calendar/{event}', [ApiPotatoEventController::class, 'calendar'])->name('api.potato.events.calendar');
        Route::get('meta', [ApiPotatoEventController::class, 'meta'])->name('api.potato.events.meta');
    });

    // Farms
    Route::prefix('farms')->group(function() {
        Route::get('index', [ApiPotatoFarmController::class, 'index'])->name('api.potato.farms.index');
        Route::post('store', [ApiPotatoFarmController::class, 'store'])->name('api.potato.farms.store');
        Route::get('show/{id}', [ApiPotatoFarmController::class, 'show'])->name('api.potato.farms.show');
        Route::get('locate/{latitude}/{longitude}', [ApiPotatoFarmController::class, 'locate'])->name('api.potato.farms.locate');
        Route::get('browse/{latitude}/{longitude}', [ApiPotatoFarmController::class, 'browse'])->name('api.potato.farms.browse');
        Route::get('search', [ApiPotatoFarmController::class, 'search'])->name('api.potato.farms.search');
        Route::match(['PUT', 'PATCH'], 'update-contact-information/{id}', [ApiPotatoFarmController::class, 'updateContactInformation'])->name('api.potato.farms.update-contact-information');
        Route::match(['PUT', 'PATCH'], 'update-description/{id}', [ApiPotatoFarmController::class, 'updateDescription'])->name('api.potato.farms.update-description');
        Route::match(['PUT', 'PATCH'], 'update-social-media/{id}', [ApiPotatoFarmController::class, 'updateSocialMedia'])->name('api.potato.farms.update-social-media');
        Route::match(['PUT', 'PATCH'], 'deactivate/{id}', [ApiPotatoFarmController::class, 'deactivate'])->name('api.potato.farms.deactivate');
    });

    // Favorites
    Route::prefix('favorites')->group(function() {
        Route::post('store/{type}/{id}', [ApiPotatoFavoriteController::class, 'store'])->name('api.potato.favorites.store');
        Route::delete('{favorite}', [ApiPotatoFavoriteController::class, 'destroy'])->name('api.potato.favorites.destroy');
    });

    // Images
    Route::prefix('images')->group(function() {
        Route::post('store/{type}/{id}', [ApiPotatoImageController::class, 'store'])->name('api.potato.images.store');
        Route::match(['PUT', 'PATCH'], 'update/{id}/{type}/{imageableId}', [ApiPotatoImageController::class, 'update'])->name('api.potato.images.update');
        Route::match(['PUT', 'PATCH'], 'update-cover/{id}/{type}/{imageableId}', [ApiPotatoImageController::class, 'updateCover'])->name('api.potato.images.update-cover');
        Route::match(['PUT', 'PATCH'], 'update-primary/{id}/{type}/{imageableId}', [ApiPotatoImageController::class, 'updatePrimary'])->name('api.potato.images.update-primary');
        Route::delete('{id}/{type}/{imageableId}', [ApiPotatoImageController::class, 'destroy'])->name('api.potato.images.destroy');
        Route::get('stream/{file}', [ApiPotatoImageController::class, 'stream'])->name('api.potato.images.stream');
    });

    // Inventory
    Route::prefix('inventory')->group(function() {
        Route::get('index', [ApiPotatoInventoryController::class, 'index'])->name('api.potato.inventory.index');
        Route::get('categories', [ApiPotatoInventoryController::class, 'categories'])->name('api.potato.inventory.categories');
        Route::get('show/{id}', [ApiPotatoInventoryController::class, 'show'])->name('api.potato.inventory.show');
    });

    // Languages
    Route::prefix('languages')->group(function() {
        Route::get('index', [ApiPotatoLanguageController::class, 'index'])->name('api.potato.languages.index');
    });

    // Markets
    Route::prefix('markets')->group(function() {
        Route::get('index', [ApiPotatoMarketController::class, 'index'])->name('api.potato.markets.index');
        Route::post('store', [ApiPotatoMarketController::class, 'store'])->name('api.potato.markets.store');
        Route::get('show/{id}', [ApiPotatoMarketController::class, 'show'])->name('api.potato.markets.show');
        Route::get('locate/{latitude}/{longitude}', [ApiPotatoMarketController::class, 'locate'])->name('api.potato.markets.locate');
        Route::get('browse/{latitude}/{longitude}', [ApiPotatoMarketController::class, 'browse'])->name('api.potato.markets.browse');
        Route::get('search', [ApiPotatoMarketController::class, 'search'])->name('api.potato.markets.search');
        Route::match(['PUT', 'PATCH'], 'update-contact-information/{id}', [ApiPotatoMarketController::class, 'updateContactInformation'])->name('api.potato.markets.update-contact-information');
        Route::match(['PUT', 'PATCH'], 'update-description/{id}', [ApiPotatoMarketController::class, 'updateDescription'])->name('api.potato.markets.update-description');
        Route::match(['PUT', 'PATCH'], 'update-social-media/{id}', [ApiPotatoMarketController::class, 'updateSocialMedia'])->name('api.potato.markets.update-social-media');
        Route::match(['PUT', 'PATCH'], 'deactivate/{id}', [ApiPotatoMarketController::class, 'deactivate'])->name('api.potato.markets.deactivate');
    });

    // Messages
    Route::prefix('messages')->group(function() {
        Route::post('store/{type}/{id}', [ApiPotatoMessageController::class, 'store'])->name('api.potato.messages.store');
        Route::post('reply/{token}', [ApiPotatoMessageController::class, 'reply'])->name('api.potato.messages.reply');
        Route::get('show/{token}', [ApiPotatoMessageController::class, 'show'])->name('api.potato.messages.show');
        Route::delete('{token}', [ApiPotatoMessageController::class, 'destroy'])->name('api.potato.messages.destroy');
        Route::post('destroy-batch', [ApiPotatoMessageController::class, 'destroyBatch'])->name('api.potato.messages.destroy-batch');
    });

    // Operating Hours
    Route::prefix('operating-hours')->group(function() {
       Route::post('save/{type}/{id}', [ApiPotatoOperatingHourController::class, 'save'])->name('api.potato.operating-hours.save');
       Route::post('save-batch/{type}/{id}', [ApiPotatoOperatingHourController::class, 'saveBatch'])->name('api.potato.operating-hours.save-batch');
       Route::get('meta', [ApiPotatoOperatingHourController::class, 'meta'])->name('api.potato.operating-hours.meta');
    });

    // Password
    Route::prefix('password')->group(function() {
        Route::post('email', [ApiPotatoResetPasswordController::class, 'email'])->name('password.email');
        Route::post('reset', [ApiPotatoResetPasswordController::class, 'reset'])->name('password.update');
    });

    // Prices
    Route::prefix('prices')->group(function() {
        Route::get('analytics/{id}', [ApiPotatoPriceController::class, 'analytics'])->name('api.potato.prices.analytics');
    });

    // Products
    Route::prefix('products')->group(function() {
        Route::post('save/{type}/{id}', [ApiPotatoProductController::class, 'save'])->name('api.potato.products.save');
        Route::get('growing-area/{latitude}/{longitude}', [ApiPotatoProductController::class, 'growingArea'])->name('api.potato.products.growing-area');
        Route::get('top-growing-areas/{id}', [ApiPotatoProductController::class, 'topGrowingAreas'])->name('api.potato.products.top-growing-areas');
        Route::get('top-selling-areas/{id}', [ApiPotatoProductController::class, 'topSellingAreas'])->name('api.potato.products.top-selling-areas');
    });

    // Registration
    Route::post('register', [ApiPotatoRegisterController::class, 'register'])->name('api.potato.register');

    // Reviews
    Route::prefix('reviews')->group(function() {
        Route::post('store/{type}/{id}', [ApiPotatoReviewController::class, 'store'])->name('api.potato.reviews.store');
    });

    // Units
    Route::prefix('units')->group(function() {
        Route::get('meta', [ApiPotatoUnitController::class, 'meta'])->name('api.potato.units.meta');
    });

    // Users
    Route::prefix('users')->group(function() {
        Route::get('current', [ApiPotatoUserController::class, 'current'])->name('api.potato.users.current');
        Route::match(['PUT', 'PATCH'], 'update-contact-information', [ApiPotatoUserController::class, 'updateContactInformation'])->name('api.potato.users.update-contact-information');
        Route::match(['PUT', 'PATCH'], 'update-country', [ApiPotatoUserController::class, 'updateCountry'])->name('api.potato.users.update-country');
        Route::match(['PUT', 'PATCH'], 'update-currency', [ApiPotatoUserController::class, 'updateCurrency'])->name('api.potato.users.update-currency');
        Route::match(['PUT', 'PATCH'],'update-language', [ApiPotatoUserController::class, 'updateLanguage'])->name('api.potato.users.update-language');
        Route::match(['PUT', 'PATCH'], 'update-password', [ApiPotatoUserController::class, 'updatePassword'])->name('api.potato.users.update-password');
    });

});
