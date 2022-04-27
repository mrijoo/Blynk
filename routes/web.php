<?php

use App\Http\Controllers\API;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/', [App::class, 'index'])->name('dashboard');
    Route::post('/', [App::class, 'update'])->name('update');
    Route::post('/addButton', [App::class, 'store'])->name('addButton');
    Route::get('/deleteButton/{id}', [App::class, 'destroy'])->name('deleteButton');
    Route::get('/api/{action}', [App::class, 'API'])->name('API');
    Route::post('/api', [App::class, 'API'])->name('API');
});
