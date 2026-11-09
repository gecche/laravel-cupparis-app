<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoormActionController;

    Route::post('queue/{foormaction}/{foorm}/{foormtype}/{constraintField}/{constraintValue}/{foormpk?}', [\App\Http\Controllers\FoormActionQueueController::class,'foormCAction'])->where([
        'foorm' => $whereFoorm,
        'foormaction' => $whereFoormCAction
    ]);
    Route::post('{foormcaction}/{foorm}/{foormtype}/{constraintField}/{constraintValue}/{$foormPk?}', [FoormActionController::class, 'foormCAction'])->where([
        'foorm' => $whereFoorm,
        'foormcaction' => $whereFoormCAction
    ]);


