<?php

use brain\Http\Controllers\HyperTargetingController;
use Illuminate\Http\Request;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/hypertargeting/allowed_fields/{template_id}', function ($template_id) {
    return response()->json([
        'fields' => HyperTargetingController::allowedFields($template_id)
    ]);
});