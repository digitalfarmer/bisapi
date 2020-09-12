<?php

namespace App\stockopname;

use Illuminate\Database\Eloquent\Model;

class StockOpnameMapping extends Model
{
    protected $table='in_stock_opname_mapping_header';
    protected $id='id';
    protected $fillable =[
        'id'           ,    
        'no_kertas_kerja',  
        'id_adjustment' 
    ];
    public $timestamps = false;
}