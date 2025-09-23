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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::redirect('/', '/dashboard/estoque');
// Dashboard Routes - TV Display
Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/estoque', [EstoqueDashboardController::class, 'index'])->name('dashboard.estoque');
});
