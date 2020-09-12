<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sl_terima_barang_retur_model extends Model
{
    protected $table = 'sl_terima_barang_retur';     
    protected $id='No_TBR';   
    protected $fillable=
                        [
                        'No_BRB_Manual',   
                        'No_TBR',          
                        'Kode_Pelanggan',  
                        'Kode_Gudang',     
                        'Tgl_TBR',         
                        'Status_Tercetak', 
                        'Status',          
                        'No_Depo',         
                        'Time_Stamp',      
                        'User_ID'
                        ] ;                        
    public $timestamps=False;                        

}
