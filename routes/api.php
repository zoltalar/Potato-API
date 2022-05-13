<?php

// --------------------------------------------------
// Admin
// --------------------------------------------------

use App\Http\Controllers\Api\Admin\AdminController as ApiAdminController;
use App\Http\Controllers\Api\Admin\AuthenticationController as ApiAdminAuthenticationController;
use App\Http\Controllers\Api\Admin\CategoryController as ApiAdminCategoryController;
use App\Http\Controllers\Api\Admin\CityController as ApiAdminCityController;
use App\Http\Controllers\Api\Admin\CountryController as ApiAdminCountryController;
use App\Http\Controllers\Api\Admin\InventoryController as ApiAdminInventoryController;
use App\Http\Controllers\Api\Admin\LanguageController as ApiAdminLanguageController;
use App\Http\Controllers\Api\Admin\TranslationController as ApiAdminTranslationController;
use App\Http\Controllers\Api\Admin\UserController as ApiAdminUserController;

// --------------------------------------------------
// Potato
// --------------------------------------------------

use App\Http\Controllers\Api\Potato\AuthenticationController as ApiPotatoAuthenticationController;
use App\Http\Controllers\Api\Potato\CityController as ApiPotatoCityController;
use App\Http\Controllers\Api\Potato\CountryController as ApiPotatoCountryController;
use App\Http\Controllers\Api\Potato\FarmController as ApiPotatoFarmController;
use App\Http\Controllers\Api\Potato\InventoryController as ApiPotatoInventoryController;
use App\Http\Controllers\Api\Potato\LanguageController as ApiPotatoLanguageController;
use App\Http\Controllers\Api\Potato\RegisterController as ApiPotatoRegisterController;
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
Route::group(['prefix' => '7pyn5wd'], function() {

    // Authentication
    Route::group(['prefix' => 'authentication'], function () {
        Route::post('login', [ApiAdminAuthenticationController::class, 'login'])->name('api.admin.authentication.login');
        Route::post('logout', [ApiAdminAuthenticationController::class, 'logout'])->name('api.admin.authentication.logout');
    });

    // Admins
    Route::group(['prefix' => 'admins'], function() {
        Route::get('index', [ApiAdminController::class, 'index'])->name('api.admin.admins.index');
        Route::post('store', [ApiAdminController::class, 'store'])->name('api.admin.admins.store');
        Route::match(['PUT', 'PATCH'], 'update/{admin}', [ApiAdminController::class, 'update'])->name('api.admin.admins.update');
        Route::delete('{admin}', [ApiAdminController::class, 'destroy'])->name('api.admin.admins.destroy');
        Route::get('current', [ApiAdminController::class, 'current'])->name('api.admin.admins.current');
    });

    // Categories
    Route::group(['prefix' => 'categories'], function() {
        Route::get('index', [ApiAdminCategoryController::class, 'index'])->name('api.admin.categories.index');
        Route::post('store', [ApiAdminCategoryController::class, 'store'])->name('api.admin.categories.store');
        Route::match(['PUT', 'PATCH'], 'update/{category}', [ApiAdminCategoryController::class, 'update'])->name('api.admin.categories.update');
        Route::delete('{category}', [ApiAdminCategoryController::class, 'destroy'])->name('api.admin.categories.destroy');
        Route::get('meta', [ApiAdminCategoryController::class, 'meta'])->name('api.admin.categories.meta');
    });

    // Cities
    Route::group(['prefix' => 'cities'], function() {
        Route::get('index', [ApiAdminCityController::class, 'index'])->name('api.admin.cities.index');
        Route::post('store', [ApiAdminCityController::class, 'store'])->name('api.admin.cities.store');
        Route::match(['PUT', 'PATCH'], 'update/{city}', [ApiAdminCityController::class, 'update'])->name('api.admin.cities.update');
        Route::get('meta', [ApiAdminCityController::class, 'meta'])->name('api.admin.cities.meta');
    });

    // Countries
    Route::group(['prefix' => 'countries'], function() {
        Route::get('index', [ApiAdminCountryController::class, 'index'])->name('api.admin.countries.index');
    });

    // Inventory
    Route::group(['prefix' => 'inventory'], function() {
        Route::get('index', [ApiAdminInventoryController::class, 'index'])->name('api.admin.inventory.index');
        Route::post('store', [ApiAdminInventoryController::class, 'store'])->name('api.admin.inventory.store');
        Route::match(['PUT', 'PATCH'], 'update/{inventory}', [ApiAdminInventoryController::class, 'update'])->name('api.admin.inventory.update');
        Route::delete('{inventory}', [ApiAdminInventoryController::class, 'destroy'])->name('api.admin.inventory.destroy');
    });

    // Languages
    Route::group(['prefix' => 'languages'], function() {
        Route::get('index', [ApiAdminLanguageController::class, 'index'])->name('api.admin.languages.index');
        Route::post('store', [ApiAdminLanguageController::class, 'store'])->name('api.admin.languages.store');
        Route::match(['PUT', 'PATCH'], 'update/{language}', [ApiAdminLanguageController::class, 'update'])->name('api.admin.languages.update');
        Route::delete('{language}', [ApiAdminLanguageController::class, 'destroy'])->name('api.admin.languages.destroy');
    });

    // Translations
    Route::group(['prefix' => 'translations'], function() {
        Route::get('index', [ApiAdminTranslationController::class, 'index'])->name('api.admin.translations.index');
        Route::post('store', [ApiAdminTranslationController::class, 'store'])->name('api.admin.translations.store');
        Route::match(['PUT', 'PATCH'], 'update/{translation}', [ApiAdminTranslationController::class, 'update'])->name('api.admin.translations.update');
        Route::delete('{translation}', [ApiAdminTranslationController::class, 'destroy'])->name('api.admin.translations.destroy');
        Route::get('meta', [ApiAdminTranslationController::class, 'meta'])->name('api.admin.translations.meta');
    });

    // Users
    Route::group(['prefix' => 'users'], function() {
        Route::get('index', [ApiAdminUserController::class, 'index'])->name('api.admin.users.index');
        Route::post('store', [ApiAdminUserController::class, 'store'])->name('api.admin.users.store');
        Route::match(['PUT', 'PATCH'], 'update/{user}', [ApiAdminUserController::class, 'update'])->name('api.admin.users.update');
        Route::delete('{user}', [ApiAdminUserController::class, 'destroy'])->name('api.admin.users.destroy');
    });

});

// Potato routes
Route::group(['prefix' => 'potato'], function() {

    // Authentication
    Route::group(['prefix' => 'authentication'], function () {
        Route::post('login', [ApiPotatoAuthenticationController::class, 'login'])->name('api.potato.authentication.login');
        Route::post('logout', [ApiPotatoAuthenticationController::class, 'logout'])->name('api.potato.authentication.logout');
    });

    // Cities
    Route::group(['prefix' => 'cities'], function() {
        Route::get('index', [ApiPotatoCityController::class, 'index'])->name('api.potato.cities.index');
    });

    // Countries
    Route::group(['prefix' => 'countries'], function() {
        Route::get('index', [ApiPotatoCountryController::class, 'index'])->name('api.potato.countries.index');
    });

    // Email Verification
    Route::group(['prefix' => 'email'], function() {
        Route::match(['PUT', 'PATCH'], 'verify/{id}/{email}', [ApiPotatoVerificationController::class, 'verify'])->name('verification.verify');
        Route::post('resend', [ApiPotatoVerificationController::class, 'resend'])->name('verification.resend');
    });

    // Farms
    Route::group(['prefix' => 'farms'], function() {
        Route::post('store', [ApiPotatoFarmController::class, 'store'])->name('api.potato.farms.store');
    });

    // Inventory
    Route::group(['prefix' => 'inventory'], function() {
        Route::get('index', [ApiPotatoInventoryController::class, 'index'])->name('api.potato.inventory.index');
    });

    // Languages
    Route::group(['prefix' => 'languages'], function() {
        Route::get('index', [ApiPotatoLanguageController::class, 'index'])->name('api.potato.languages.index');
    });

    // Registration
    Route::post('register', [ApiPotatoRegisterController::class, 'register'])->name('api.potato.register');

    // Users
    Route::group(['prefix' => 'users'], function() {
        Route::get('current', [ApiPotatoUserController::class, 'current'])->name('api.potato.users.current');
        Route::post('update-country', [ApiPotatoUserController::class, 'updateCountry'])->name('api.potato.users.update-country');
        Route::post('update-language', [ApiPotatoUserController::class, 'updateLanguage'])->name('api.potato.users.update-language');
    });

});
