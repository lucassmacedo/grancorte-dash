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

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboards\EstoqueDashboardController;
use App\Http\Controllers\Dashboards\LogisticaDashboardController;
use App\Http\Controllers\Dashboards\ComercialVendedoresDashboardController;
use App\Http\Controllers\Dashboards\ComercialClientesDashboardController;
use App\Http\Controllers\Dashboards\ComercialProdutosDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

// Dashboard Routes - TV Display
Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/estoque', [EstoqueDashboardController::class, 'index'])->name('dashboard.estoque');
    Route::get('/logistica', [LogisticaDashboardController::class, 'index'])->name('dashboard.logistica');
    Route::get('/vendedores', [ComercialVendedoresDashboardController::class, 'index'])->name('dashboard.vendedores');
    Route::get('/clientes', [ComercialClientesDashboardController::class, 'index'])->name('dashboard.clientes');
    Route::get('/produtos', [ComercialProdutosDashboardController::class, 'index'])->name('dashboard.produtos');
    Route::get('/pedidos', [\App\Http\Controllers\PedidosDashboardController::class, 'index'])->name('dashboard.pedidos');
});

Route::get('/proxy-dashboard', [\App\Http\Controllers\ProxyDashboardController::class, 'show'])->name('proxy.dashboard');
