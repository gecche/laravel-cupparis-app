<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$namespace = \Illuminate\Support\Facades\Config::get('cupparis-app.namespace',"Gecche\\Cupparis\\App\\Http\\Controllers");
Route::group([
    'namespace' => $namespace,
    'middleware' => ['web']
], function () {

// route to access template applied image file
    Route::get('imagecache/{template}/{filename}', [
        'uses' => '\Intervention\Image\ImageCacheController@getResponse',
        'as' => 'imagecache'
    ])->where(['filename' => '[ \w\\.\\/\\-\\@\(\)]+']);

    Route::get('viewmediable/{model}/{pk}/{template?}', 'DownloadController@viewMediableFile');
    Route::get('downloadtemp/{nome}', 'DownloadController@downloadtemp');
    Route::get('downloadmediable/{model}/{pk}', 'DownloadController@downloadMediableFile');
    Route::get('openmediable/{model}/{pk}', 'DownloadController@openMediableFile');

});
