<?php

use App\Http\Controllers\Api\FoormController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\DownloadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/login',  [LoginController::class, 'login']);

Route::post('/forgot-password', [\App\Http\Controllers\Api\PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('api.password.email');
Route::post('/reset-password', [\App\Http\Controllers\Api\NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('api.password.update');

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    $user = $request->user();
    $user['fotos'] = $request->user()->fotos()->get()->toArray();
    return $user;
});
Route::middleware('auth:sanctum')->get('/app-menu',[FoormController::class,'getAppMenu']);

Route::any('/logout', [LoginController::class, 'logout'])
                ->middleware('auth:sanctum')
                ->name('logout');

$namespace = \Illuminate\Support\Facades\Config::get('cupparis-app.namespace',"Gecche\\Cupparis\\App\\Http\\Controllers");
Route::group([
    'namespace' => $namespace,
    'middleware' => ['auth:sanctum']
], function () {

    require __DIR__ . '/general-routes.php';

});

Route::middleware('auth:sanctum')->get('json/model-conf/{modelName}', function ($modelName) {
    try {
        return [
            'error' => 0,
            'msg' => '',
            'result' => config('foorms.' . $modelName)
        ];
    } catch (\Exception $e) {
        return [
            'error' => 1,
            'msg' => $e->getMessage(),
            'result' => []
        ];
    }

});

Route::group(['prefix' => 'superadmin','middleware' => 'auth:sanctum'], function () {
    //$whereFoorm = join("|", config('foorm.foorms',[]));

    Route::middleware('auth:sanctum')->get('/models-permessi', [\App\Http\Controllers\Api\SuperadminController::class, 'getModels']);
});