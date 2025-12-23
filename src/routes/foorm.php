<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoormController;



Route::post('uploadfile', [FoormController::class,'uploadfile']);

$configFoorms = config('foorm.foorms',[]);
$whereFoorm = join("|", $configFoorms);

$configFoormActions = config('foorm.foorm-actions',[]);
$whereFoormAction = join("|", $configFoormActions);

$configCFoormActions = config('foorm.foorm-c-actions',[]);
$whereFoormCAction = join("|", $configCFoormActions);

//print_r($cupparisJsonFoorms);die();

Route::group(['middleware' => 'web','prefix' => 'foormaction'], function () use ($whereFoorm,$whereFoormAction) {

    require __DIR__ . '/foormaction-routes.php';

});

Route::group(['middleware' => 'web','prefix' => 'foormcaction'], function () use ($whereFoorm,$whereFoormCAction) {

    require __DIR__ . '/foormcaction-routes.php';

});

Route::group(['middleware' => 'web','prefix' => 'foorm'], function () use ($whereFoorm) {

    require __DIR__ . '/foorm-routes.php';

});

Route::group(['middleware' => 'web','prefix' => 'foormc', 'as' => 'foormc'], function () use ($whereFoorm) {

    require __DIR__ . '/foormc-routes.php';

});
