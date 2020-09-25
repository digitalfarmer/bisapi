<?php

namespace App\stockopname;

use Illuminate\Database\Eloquent\Model;

class in_stock_opname_selisih extends Model
{
    protected $table='in_stock_opname_hasil';
    protected $id='no_kertas_kerja';
    protected $fillable =[
        'No_Kertas_Kerja',   
        'Kode_Barang',       
        'No_Batch',          
        'Kadaluarsa',        
        'Level',             
        'Jumlah',            
        'Referensi'              
    ];
    public $timestamps = false;
}
