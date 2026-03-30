<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$namespace = \Illuminate\Support\Facades\Config::get('cupparis-app.namespace',"Gecche\\Cupparis\\App\\Http\\Controllers");
Route::group([
    'namespace' => $namespace,
    'middleware' => ['web']
], function () {

    require __DIR__ . '/general-routes.php';

});
