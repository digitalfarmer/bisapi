<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
 
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
 
//route module stock picking BLG
Route::get('blg/getUpdatePickingItem/{picking_id}','BISAPIController@getUpdatePickingItem');   
//route module Stock Adjustment BLG
Route::get('blg/sendStockAdjustment/{adjustment_id}','ProsesKartuStokController@sendStockAdjustment');   
Route::get('blg/getStockOpname/{adjustment_id}','ProsesKartuStokController@getStockOpname'); 
Route::get('blg/get_stock/{adjustment_id}','ProsesKartuStokController@get_stock');     
Route::post('blg/flagBlockingStock/{adjustment_id}','ProsesKartuStokController@FlagBlockingStock');   
 //Cek Status Adjustment BLG
Route::post('blg/cekOpnameStatus','BISMySQLController@cekOpnameStatus');
Route::get('blg/cekDivisiProdukOpname/{Kode_Divisi_Produk}','BISMySQLController@cekDivisiProdukOpname');
 //BISMySQL numbering BLG
Route::post('blg/getNewNumber/{type_nomor}','BISMySQLController@getNewNumber');
 //create kkso BLG
Route::get('blg/createadjustment','StockOpnameController@createkkso');
//------------2020-09-30 11:05
//------------2020-09-30 13:42
