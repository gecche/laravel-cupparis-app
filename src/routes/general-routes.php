<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;

Route::get('imagecache/{template}/{filename}', [
    'uses' => '\Intervention\Image\ImageCacheController@getResponse',
    'as' => 'imagecache'
])->where(['filename' => '[ \w\\.\\/\\-\\@\(\)]+']);

Route::get('viewmediable/{model}/{pk}/{template?}', [DownloadController::class, 'viewMediableFile']);
//Route::get('viewmediable/{model}/{pk}/{template?}', function($model,$pk,$template=null) {
//    return $model;
//});

Route::get('downloadtemp/{nome}', [DownloadController::class, 'downloadtemp']);
Route::get('downloadmediable/{model}/{pk}', [DownloadController::class, 'downloadMediableFile']);
Route::get('openmediable/{model}/{pk}', [DownloadController::class, 'openMediableFile']);

Route::get('viewuploadable/{filename}/{template?}', [DownloadController::class, 'viewUploadableFile']);
Route::get('downloaduploadable/{filename}/{disposition?}', [DownloadController::class, 'downloadUploadableFile']);
