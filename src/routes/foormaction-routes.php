<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoormActionController;

    //PARAMETRI DA METTERE IN POST: id
    Route::post('migrate/cupparis_entity/list/{foormpk?}', [FoormActionController::class,'migrate']);
    Route::post('rollback/cupparis_entity/list/{foormpk?}', [FoormActionController::class,'rollback']);
    Route::post('import/cupparis_entity/list/{foormpk?}', [FoormActionController::class,'import']);
    //PARAMETRI DA METTERE IN POST: id
    Route::delete('delete/{foorm}/{foormtype}/{foormpk?}', [FoormActionController::class,'delete'])->where([
        'foorm' => $whereFoorm
    ]);

    //PARAMETRI DA METTERE IN POST: ids (array)
    Route::post('{foormaction}/{foorm}/{foormtype}/{foormpk?}', [FoormActionController::class,'postMultiDelete'])->where([
        'foorm' => $whereFoorm,
        'foormaction' => $whereFoormAction
    ]);

