<?php

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

$modules        = config('modular.modules');
$path           = config('modular.path');
$base_namespace = config('modular.base_namespace');


if ($modules) {
    foreach ($modules as $mod => $submodules) {
        foreach ($submodules as $key => $sub) {
            if (is_string($key)) {
                $sub = $key;
            }

            $relativePath = "/$mod/$sub";
            $routesPath   = "{$path}{$relativePath}/routes_api.php";

            if (file_exists($routesPath)) {
                Route::namespace("Modules\\$mod\\$sub\Controllers")
                     ->group($routesPath);
            }
        }
    }
}