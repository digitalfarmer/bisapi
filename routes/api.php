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
//Blocking Stok BISMySQL from Odoo
Route::post('blg/flagBlockingStock/{adjustment_id}','ProsesKartuStokController@FlagBlockingStock');   
Route::post('blg/cancelBlockingStock/{adjustment_id}','ProsesKartuStokController@CancelBlockingStock');   
//Cek Status Adjustment BLG
Route::post('blg/cekOpnameStatus','SequenceController@cekOpnameStatus');
Route::get('blg/cekDivisiProdukOpname/{Kode_Divisi_Produk}','SequenceController@cekDivisiProdukOpname');

//Sequence numbering
#Route::post('blg/getNewKJNumber/{type_nomor}','SequenceController@getNewKJNumber');
#Route::post('blg/getNewOCNumber/{type_nomor}','SequenceController@getNewOCNumber');
#Route::post('blg/getNewDSNumber/{type_nomor}','SequenceController@getNewDSNumber');
#Route::post('blg/getNewKCNumber/{type_nomor}','SequenceController@getNewKCNumber');
Route::post('blg/getNewNumber','SequenceController@getNewNumber');


//create kkso BLG
Route::get('blg/createadjustment','StockOpnameController@createkkso');
//BISMySQL Peminjaman/Spreading dari Odoo 
Route::post('blg/postPickingSpreading/{picking_id}','SpreadingController@postPickingSpreading');
 