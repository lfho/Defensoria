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

Route::prefix('visit')->group(function() {
    Route::get('/', 'VisitController@index');

// Ruta para la gestion de citizens
Route::resource('citizens', 'CitizenController', ['only' => [
    'index', 'store', 'update', 'destroy'
]]);
// Ruta que obtiene todos los registros de citizens
Route::get('get-citizens', 'CitizenController@all')->name('all');
// Ruta para exportar los datos de citizens
Route::post('export-citizens', 'CitizenController@export')->name('export');
});

