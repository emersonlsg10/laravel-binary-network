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
Route::get('/', "SistemaController@index")->name("sistema.index");

Route::post('/adicionar', "VendedoresController@adicionar")->name("vendedor.adicionar");

Route::post('/gerar-relatorio', "VendedoresController@relatorio")->name("relatorio");