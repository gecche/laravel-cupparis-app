<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoormController;
use App\Http\Controllers\JsonController;

Route::get('json/dynamic-conf', [JsonController::class,'postDynamicConf'])->name('json-dynamic-conf');
Route::post('json/user-info', [JsonController::class,'getUserInfo'])->name('json-user-info');

Route::middleware('auth:sanctum')->post('uploadfile', [FoormController::class,'uploadfile']);

$configFoorms = config('foorm.foorms',[]);
$whereFoorm = join("|", $configFoorms);

$configFoormActions = config('foorm.foorm-actions',[]);
$whereFoormAction = join("|", $configFoormActions);

$configCFoormActions = config('foorm.foorm-c-actions',[]);
$whereFoormCAction = join("|", $configCFoormActions);

//print_r($cupparisJsonFoorms);die();

Route::group(['middleware' => ['api','auth:sanctum'],'prefix' => 'foormaction'], function () use ($whereFoorm,$whereFoormAction) {

    require __DIR__ . '/foormaction-routes.php';

});

Route::group(['middleware' => ['api','auth:sanctum'],'prefix' => 'foormcaction'], function () use ($whereFoorm,$whereFoormCAction) {

    require __DIR__ . '/foormcaction-routes.php';

});

Route::group(['middleware' => ['api','auth:sanctum'],'prefix' => 'foorm'], function () use ($whereFoorm) {

    require __DIR__ . '/foorm-routes.php';

});

Route::group(['middleware' => ['api','auth:sanctum'],'prefix' => 'foormc', 'as' => 'foormc'], function () use ($whereFoorm) {

    require __DIR__ . '/foormc-routes.php';

});
