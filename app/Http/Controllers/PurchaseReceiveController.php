<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\pc_barang_datang_model;
use App\pc_barang_datang_detail_model;
use App\pc_barang_datang_harga_model;
use Illuminate\Http\Request;

class PurchaseReceiveController extends Controller
{
    public function getReceiving() 
    {
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect(); 
     
        $result = $odoo->call(
                            'stock.picking', 
                            'get_summarize_move_line',                           
                            [['BPB1111'],['DO11111']]           
                            #['DO11111','BPB1111']           
                           );
        return($result);       

 
        
    }  //
}
