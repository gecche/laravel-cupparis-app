<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoormController;

Route::get('{foorm}/search/{type?}', [FoormController::class,'getSearch'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/new/{type?}', [FoormController::class,'getNew'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/{datafileId}/datafile-list/{type?}', [FoormController::class,'getDatafileList'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/list/{type}', [FoormController::class,'getList'])->where([
    'foorm' => $whereFoorm
]);
Route::get('{foorm}/{id}/edit/{type?}', [FoormController::class,'getEdit'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/{id}/{type?}', [FoormController::class,'getShow'])->where([
    'foorm' => $whereFoorm
]);

Route::post('{foorm}/{type?}', [FoormController::class,'postCreate'])->where([
    'foorm' => $whereFoorm
]);

Route::put('{foorm}/{id}/{type?}', [FoormController::class,'postUpdate'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/{type?}', [FoormController::class,'getList'])->where([
    'foorm' => $whereFoorm
]);

