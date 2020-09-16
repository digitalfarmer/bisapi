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

 //Route::get('students2','StudentsController@students2');
 //Route::get('getstock_move_line/{no_delivery}','ApiOdooController@get_rqStockMoveLine');
 #Route::get('bismysql/getStockMoveLineRow/{picking_id}','BISAPIController@getStockMoveLineRow');
 Route::get('bismysql/getUpdatePickingItem/{picking_id}','BISAPIController@getUpdatePickingItem');  
 Route::get('bismysql/sentStockAdjusment/{adjustment_id}','BISAPIController@sentStockAdjusment');   
 Route::get('bismysql/getSessionID','BISAPIController@getSessionID');  
 Route::get('bismysql/KartuStokAdjustment/{no_kertas_kerja}','BISAPIController@KartuStokAdjustment');#Untuk Insert data ke in_kartu_stok_detail  #
 
 Route::get('stockopname','StockOpnameController@index');
 Route::post('stockopname','StockOpnameController@store');
 

