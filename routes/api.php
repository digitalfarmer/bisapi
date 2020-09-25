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
 
//route module stock picking
  
 Route::get('bismysql/getUpdatePickingItem/{picking_id}','BISAPIController@getUpdatePickingItem');  
 Route::get('bismysql/getStockOpname/{adjustment_id}','ProsesKartuStokController@getStockOpname'); 
 Route::get('bismysql/get_stock/{adjustment_id}','ProsesKartuStokController@get_stock');   
 #Route::get('bismysql/sendStockAdjustment/{adjustment_id}','ProsesKartuStokController@sendStockAdjustment');   
 Route::get('bismysql/sendStockAdjustment2/{adjustment_id}','ProsesKartuStokController@sendStockAdjustment2');   
 Route::post('bismysql/flagBlockingStock/{adjustment_id}','ProsesKartuStokController@FlagBlockingStock');   
 
   
 
 //create kkso
Route::get('/createadjustment','StockOpnameController@createkkso');

