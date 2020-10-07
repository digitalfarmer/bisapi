<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\stockopname\StockOpname; 
use App\sy_konfigurasi_model;
use App\mapping\in_stock_opname_blocking_model; 
use App\Spreading\sr_peminjaman_model;

class SequenceController extends Controller
{
    public function getNewKJNumber(Request $request)
    {   
        $type_nomor = $request->type_nomor;
        if($type_nomor='KJ'){
            $tanggal    = New Carbon($request->tanggal_transaksi);
            #return($tanggal);
            $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
            $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;             
            
            $NoKJ_BIS   = StockOpname::select('no_kertas_kerja')    
                                      ->whereRaw('MONTH(Tanggal) = ?',$bln)
                                      ->whereRaw('YEAR(Tanggal) = ?', $thn)                                
                                      ->orderBy('no_kertas_kerja','desc')     
                                      ->limit('1')
                                      ->get();
    #       return($NoKJ_BIS);
            if (count($NoKJ_BIS)>0){                       
                $TLast_Number = substr($NoKJ_BIS,-8,5);
            } else{
                $TLast_Number = 0; 
            }       
        
            $lastNumber = $TLast_Number+1;      

            $pr_id = sprintf("%05d", $lastNumber);


            $branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                                ->select('Nilai')
                                                ->get();
            $prefix_kj = $branchCode[0]['Nilai'];
            $padbln    = str_pad($bln,2,"0",STR_PAD_LEFT);

            $no_kj     ='KJ'.$prefix_kj.'/'.$thn.$padbln.'/'.$pr_id;
                
            response()->json([                     
                            'new_number'=>$no_kj
                            ])->send();    
        }
                        
        
    }

    public function getNewOCNumber(Request $request)
    {   
        
        $type_nomor = $request->type_nomor;
        if($type_nomor='OC'){
            $tanggal    = New Carbon($request->tanggal_transaksi);
            #========================================================================
            $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
            $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;             
            #=========================================================================
            $No_Peminjaman = sr_peminjaman_model::select('No_Peminjaman')    
                                                ->whereRaw('MONTH(Tanggal_Pinjam) = ?',$bln)
                                                ->whereRaw('YEAR(Tanggal_Pinjam) = ?', $thn)                                
                                                ->orderBy('No_Peminjaman','desc')     
                                                ->limit('1')
                                                ->get();

             

            if (count($No_Peminjaman)>0){                       
                $TLast_Number = substr($No_Peminjaman,-8,5);
            } else{
                $TLast_Number = 0; 
            }       
        
            $lastNumber = $TLast_Number+1;      

            $pr_id = sprintf("%05d", $lastNumber);


            $branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                                ->select('Nilai')
                                                ->get();
            $prefix_kj = $branchCode[0]['Nilai'];
            $padbln    = str_pad($bln,2,"0",STR_PAD_LEFT);

            $no_oc='OC'.$prefix_kj.'/'.$thn.$padbln.'/'.$pr_id;
                
            response()->json([                   
                            'new_number'=>$no_oc
                            ])->send();                
        }            
        
    }

    public  function cekOpnameStatus(Request $request)
    {
        $OnOpnameStock = in_stock_opname_blocking_model::where('Status_Adjustment','=','progress')                                                      
                                                        ->where('Kode_Divisi_Produk','=',$request->Kode_Divisi_Produk)                                            
                                                        ->select('no_kkso','adjustment_id', 'Kode_Divisi_Produk','Status_Adjustment')
                                                        ->get();
                            
        if (count($OnOpnameStock)>0) {
            response()->json([
                             'opname_status'=>1,                           
                             'kkso_status'=> $OnOpnameStock
                             ])->send();        
        } 
        else
        {    
            response()->json(['opname_status'=>0])->send();       
        }       

    }

    public  function cekDivisiProdukOpname(Request $request)
    {
        $OnOpnameStock = in_stock_opname_blocking_model::where('Status_Adjustment','=','progress')                                                         
                                                        ->where('Kode_Divisi_Produk','=',$request->Kode_Divisi_Produk)                                            
                                                        ->select('Kode_Divisi_Produk')
                                                        ->limit(1)
                                                        ->get();
                                                         
                            
        if (count($OnOpnameStock)>0) {
                              response()->json([
                             'opname_status'=>1,                           
                             'kkso_status'=> $OnOpnameStock
                             ])->send();        
        } 
        else{    
            response()->json(['opname_status'=>0])->send();       
        }       

    }
    

}