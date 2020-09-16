<?php
namespace App\Http\Controllers;

use App\ms_barang_satuan_model;
use App\in_stock_opname_model;
use App\in_stock_opname_selisih_model;
use App\in_kartu_stok_detail_model;
use Session;

use Illuminate\Http\Request;

class ProsesKartuStokController extends Controller
{
    public function KartuStokAdjustment($no_kertas_kerja)
    {      

        $no_ref = substr($no_kertas_kerja,0,5).'/'.substr($no_kertas_kerja,5,6).'/'.substr($no_kertas_kerja,11,5);
                                    
        $opname = in_stock_opname_model::where('in_stock_opname.no_kertas_kerja',$no_ref) 
                                        ->where('in_stock_opname.Status','POST')
                                        ->join('in_stock_opname_selisih','in_stock_opname.no_kertas_kerja','=','in_stock_opname_selisih.no_kertas_kerja')
                                        ->join('ms_barang_satuan', function($join)
                                                        {
                                                            $join->on('ms_barang_satuan.kode_barang','=','in_stock_opname_selisih.kode_barang');
                                                            $join->on('ms_barang_satuan.level','=','in_stock_opname_selisih.level');
                                                        } 
                                        )                                                   
                                        ->select(
                                        DB::raw('date_format(in_stock_opname.tanggal,"%Y%m") Periode'),
                                        DB::raw('"ADJUSTMENT" Jenis_Transaksi'),
                                        'in_stock_opname.Tanggal as Tgl_Transaksi',
                                        'in_stock_opname.No_Kertas_Kerja',     
                                        'in_stock_opname.Kode_Principal',  
                                        'in_stock_opname.Kode_Divisi_Produk',  
                                        'in_stock_opname.Kode_Gudang',  
                                        DB::raw('case in_stock_opname.status_barang '.
                                        'when "BAIK" then "AVAILABLE" '.
                                        'when "SEMI RUSAK" then "DEFECT" '.
                                        'when "RUSAK" then "REJECT" '.
                                        'when "BERMASALAH" then "PENDING" '.
                                        'when "HOLD" then "HOLD" '.
                                        'end as Status_Barang'),
                                        'in_stock_opname_selisih.Kode_Barang',  
                                        'in_stock_opname_selisih.No_Batch',    
                                        'in_stock_opname_selisih.Kadaluarsa',
                                        DB::raw('sum(in_stock_opname_selisih.jumlah) as Jumlah'),        
                                        'ms_barang_satuan.Harga_Beli',                                 
                                        'in_stock_opname_selisih.Level',                                        
                                        'ms_barang_satuan.Satuan',
                                        'in_stock_opname_selisih.Referensi',
                                        DB::raw('Now() as TimeStamp')
                                        )                                 
                                        ->groupBy(
                                        DB::raw('date_format(in_stock_opname.tanggal,"%Y%m")'),
                                        'in_stock_opname.Tanggal',
                                        'Jenis_Transaksi',
                                        'in_stock_opname.No_Kertas_Kerja',     
                                        'in_stock_opname.Kode_Principal',  
                                        'in_stock_opname.Kode_Divisi_Produk',  
                                        'in_stock_opname.Kode_Gudang',  
                                        'in_stock_opname.Tanggal',   
                                        'in_stock_opname.status_barang',                                                                                    
                                        'in_stock_opname_selisih.No_Kertas_Kerja',     
                                        'in_stock_opname_selisih.Kode_Barang',  
                                        'in_stock_opname_selisih.No_Batch',     
                                        'in_stock_opname_selisih.Kadaluarsa', 
                                        'ms_barang_satuan.Harga_Beli',  
                                        'in_stock_opname_selisih.Level',
                                        'ms_barang_satuan.Satuan',
                                        'in_stock_opname_selisih.Referensi',
                                        'TimeStamp'
                                        ) 
                                        ->get();
        
        $rowCount=0;
 
        foreach ($opname as  $opnameItem)
        {
                $OpnameData[$rowCount]['Periode']         =  $opnameItem['Periode'];
                $OpnameData[$rowCount]['No_Transaksi']    =  $opnameItem['No_Kertas_Kerja'];
                $OpnameData[$rowCount]['Jenis_Transaksi'] =  $opnameItem['Jenis_Transaksi'];
                $OpnameData[$rowCount]['Tgl_Transaksi']   =  $opnameItem['Tgl_Transaksi'];    
                $OpnameData[$rowCount]['Barang']          =  $opnameItem['Kode_Barang'];        
                $OpnameData[$rowCount]['Gudang']          =  $opnameItem['Kode_Gudang'];  
                $OpnameData[$rowCount]['STATUS']          =  $opnameItem['Status_Barang'];
                $OpnameData[$rowCount]['Batch']           =  $opnameItem['No_Batch'];
                $OpnameData[$rowCount]['Level_Asal']      =  $opnameItem['Level'];
                $OpnameData[$rowCount]['Net']             =  $opnameItem['Jumlah'];
                $OpnameData[$rowCount]['Harga_Beli']      =  $opnameItem['Harga_Beli'];
                $OpnameData[$rowCount]['Keterangan']         =  'LAINLAIN';
                $OpnameData[$rowCount]['ID_Program_Promosi'] =  '';
                $OpnameData[$rowCount]['Expired']            =  $opnameItem['Kadaluarsa'];
                if($OpnameData[$rowCount]['Expired']!="0000-00-00") {
                    $OpnameData[$rowCount]['Expired']        =  $opnameItem['Kadaluarsa'];
                }
                else {
                    $OpnameData[$rowCount]['Expired']        = '1999-09-09';
                }             
                
                $OpnameData[$rowCount]['TimeStamp']          =  $opnameItem['TimeStamp'];
                $rowCount++;
        }
        
       // return($OpnameData);
        if (count($OpnameData)>0) 
        {  
                return($OpnameData);
    
        } 
    }
}
