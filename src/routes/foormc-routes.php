<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoormController;

Route::get('{foorm}/search/{constraintField}/{constraintValue}/{type?}', [FoormController::class,'getSearchConstrained'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/new/{constraintField}/{constraintValue}/{type?}', [FoormController::class,'getNewConstrained'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/list/{constraintField}/{constraintValue}/{type?}', [FoormController::class,'getListConstrained'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/{id}/edit/{constraintField}/{constraintValue}/{type?}', [FoormController::class,'getEditConstrained'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/{id}/{constraintField}/{constraintValue}/{type?}', [FoormController::class,'getShowConstrained'])->where([
    'foorm' => $whereFoorm
]);

Route::post('{foorm}/{constraintField}/{constraintValue}/{type?}', [FoormController::class,'postCreateConstrained'])->where([
    'foorm' => $whereFoorm
]);

Route::put('{foorm}/{id}/{constraintField}/{constraintValue}/{type?}', [FoormController::class,'postUpdateConstrained'])->where([
    'foorm' => $whereFoorm
]);

Route::get('{foorm}/{constraintField}/{constraintValue}/{type?}', [FoormController::class,'getListConstrained'])->where([
    'foorm' => $whereFoorm
]);

