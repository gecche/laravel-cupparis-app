<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoormActionController;

    Route::post('flush-datafile/{foorm}/{foormtype}/{constraintField}/{constraintValue}', [FoormActionController::class, 'flushDatafileConstrained'])->where([
        'foorm' => $whereFoorm
    ]);


