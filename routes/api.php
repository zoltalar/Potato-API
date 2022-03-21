<?php

// --------------------------------------------------
// Admin
// --------------------------------------------------

use App\Http\Controllers\Api\Admin\AdminController as ApiAdminController;
use App\Http\Controllers\Api\Admin\AuthenticationController as ApiAdminAuthenticationController;
use App\Http\Controllers\Api\Admin\CityController as ApiAdminCityController;
use App\Http\Controllers\Api\Admin\CountryController as ApiAdminCountryController;
use App\Http\Controllers\Api\Admin\LanguageController as ApiAdminLanguageController;

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

    // Cities
    Route::group(['prefix' => 'cities'], function() {
        Route::get('index', [ApiAdminCityController::class, 'index'])->name('api.admin.cities.index');
        Route::post('store', [ApiAdminCityController::class, 'store'])->name('api.admin.cities.store');
        Route::get('meta', [ApiAdminCityController::class, 'meta'])->name('api.admin.cities.meta');
    });

    // Countries
    Route::group(['prefix' => 'countries'], function() {
        Route::get('index', [ApiAdminCountryController::class, 'index'])->name('api.admin.countries.index');
    });

    // Languages
    Route::group(['prefix' => 'languages'], function() {
        Route::get('index', [ApiAdminLanguageController::class, 'index'])->name('api.admin.languages.index');
        Route::post('store', [ApiAdminLanguageController::class, 'store'])->name('api.admin.languages.store');
        Route::match(['PUT', 'PATCH'], 'update/{language}', [ApiAdminLanguageController::class, 'update'])->name('api.admin.languages.update');
        Route::delete('{language}', [ApiAdminLanguageController::class, 'destroy'])->name('api.admin.languages.destroy');
    });

});
