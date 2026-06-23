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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Residenciales\ResidencialesController;
use App\Http\Controllers\Clientes\ClientesController;
use App\Http\Controllers\Ventas\VentasController;
use App\Http\Controllers\DashboardController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index']);

    Route::group(['prefix' => 'residenciales'], function(){
        Route::get('/', [ResidencialesController::class, 'ver_residenciales']);
        Route::post('/guardar', [ResidencialesController::class, 'guardar_residencial']);
        Route::get('{id_residencial}/bloques', [ResidencialesController::class, 'ver_bloques']);
        Route::post('/bloques/guardar', [ResidencialesController::class, 'guardar_bloque']);
        Route::get('{id_residencial}/bloques/{id_bloque}', [ResidencialesController::class, 'ver_lotes']);
        Route::post('/bloques/lotes/guardar', [ResidencialesController::class, 'guardar_lote']);
    });

    Route::group(['prefix' => 'clientes'], function(){
        Route::get('/', [ClientesController::class, 'ver_clientes']);
        Route::get('/datos', [ClientesController::class, 'datos_clientes']);
        Route::get('/buscar', [ClientesController::class, 'buscar_clientes']);
        Route::post('/guardar', [ClientesController::class, 'guardar_cliente']);
        Route::post('/obtener-referencias', [ClientesController::class, 'obtener_referencias']);
        Route::post('/obtener-beneficiarios', [ClientesController::class, 'obtener_beneficiarios']);
        Route::get('/perfil/{id}', [ClientesController::class, 'perfil_cliente']);
    });

    Route::group(['prefix' => 'ventas'], function(){
        Route::get('/', [VentasController::class, 'ver_ventas']);
        Route::post('/guardar', [VentasController::class, 'guardar_venta']);
        Route::get('/detalle/{id}', [VentasController::class, 'ver_detalle_venta']);
        Route::post('/pagar-cuota', [VentasController::class, 'pagar_cuota']);
    });

    Route::get('/vender', [VentasController::class, 'ver_vender']);

    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        return "Cache is cleared";
    });

    // 404 for undefined routes
    Route::any('/{page?}',function(){
        return View::make('pages.error.404');
    })->where('page','.*');
});
