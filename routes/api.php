<?php

use Illuminate\Http\Request;
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
Route::group(['prefix' => 'v1', 'middleware' => ['cors']], function () {
    Route::get('/categories', 'App\Http\Controllers\CategoryController@index');
    Route::get('/categories/tree', 'App\Http\Controllers\CategoryController@getListTree');
    Route::get('/showroom', 'App\Http\Controllers\ShowroomController@index');
    Route::get('/banner', 'App\Http\Controllers\BannerController@getBanners');
    Route::get('/artifacts', 'App\Http\Controllers\ArtifactController@getArtifacts');
    Route::get('/artifacts/detail/{id}', 'App\Http\Controllers\ArtifactController@detailArtifact');
    Route::get('/artifacts/detail-by-code', 'App\Http\Controllers\ArtifactController@detailArtifactByCode');
    Route::get('/ar/detail', 'App\Http\Controllers\ArController@detail');
    Route::get('/post', 'App\Http\Controllers\PostController@index');
    Route::get('/post/detail/{id}', 'App\Http\Controllers\PostController@detailPost');
});
Route::group(['middleware' => ['cors']], function () {
    Route::get('language', [
        'as' => 'api.language.all',
        'uses' => 'App\Http\Controllers\LanguageController@getListLanguage'
    ]);
    Route::get('system-settings', [
        'as' => 'api.system.settings',
        'uses' => 'App\Http\Controllers\SettingController@getSystemSetting'
    ]);
});
