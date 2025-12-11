<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoormActionController;

    Route::post('{foormcaction}/{foorm}/{foormtype}/{constraintField}/{constraintValue}/{$foormPk?}', [FoormActionController::class, 'foormCAction'])->where([
        'foorm' => $whereFoorm,
        'foormcaction' => $whereFoormCAction
    ]);


