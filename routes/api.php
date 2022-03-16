<?php

// --------------------------------------------------
// Admin
// --------------------------------------------------

use App\Http\Controllers\Api\Admin\AuthenticationController as ApiAdminAuthenticationController;

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

});
