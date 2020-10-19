<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\stockopname\StockOpname; 
use App\sy_konfigurasi_model;
use App\mapping\in_stock_opname_blocking_model; 
use App\Spreading\sr_peminjaman_model;
use App\Spreading\sr_pengembalian_model;
use App\Spreading\sr_pemfakturan_model;
use App\Sales\sl_faktur_model;
use App\Sales\sl_surat_pesanan_model;
use App\in_delivery_model;
use App\Purchase\pc_barang_datang_model;
use Illuminate\Support\Facades\DB;

class SequenceController extends Controller
{    
    public  function getNewNumber($type_nomor, $tanggal_transaksi)
    {       
        if($type_nomor=='KJ'){
            $nomor = $this->getNewKJNumber($type_nomor,$tanggal_transaksi);            
        } 
        else if($type_nomor=='OC'){
            $nomor =  $this->getNewOCNumber($type_nomor,$tanggal_transaksi);            
        } 
        else if(($type_nomor=='DS') || 
                ($type_nomor=='DM') || 
                ($type_nomor=='DO') )  {
            $nomor =  $this->getNewDeliveryNumber($type_nomor, $tanggal_transaksi);  
        } 
        else if($type_nomor=='KC'){
            $nomor =  $this->getNewKCNumber($type_nomor,$tanggal_transaksi);            
        } 
        else if($type_nomor=='FC'){
            $nomor =  $this->getNewFCNumber($type_nomor,$tanggal_transaksi);            
        } 
        else if($type_nomor=='FK'){
            $nomor =  $this->getNewFKNumber($type_nomor,$tanggal_transaksi);            
        }   
        else if($type_nomor=='SP'){
            $nomor =  $this->getNewSPNumber($type_nomor,$tanggal_transaksi);            
        }  
        else if($type_nomor=='BD'){
            $nomor =  $this->getNewBDNumber($type_nomor,$tanggal_transaksi);            
        }    
        return  $nomor;           
    }

    public function getBranchCode()
    {
        $branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                            ->select('Nilai')
                                            ->get();
        $prefix_cabang = $branchCode[0]['Nilai'];

        return $prefix_cabang;    
    }

     
    public function getNewSPNumber($type_nomor,$tanggal_transaksi)
    {
        $tanggal    = New Carbon($tanggal_transaksi);    
        $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
        $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;      

        $NoSP_BIS   = sl_surat_pesanan_model::select('No_SP')    
                                            ->whereRaw('MONTH(Tgl_SP) = ?',$bln)
                                            ->whereRaw('YEAR(Tgl_SP) = ?', $thn)                                
                                            ->orderBy('No_SP','desc')     
                                            ->limit('1')
                                            ->get();
                            
        if (count($NoSP_BIS)>0){                       
            $TLast_Number = substr($NoSP_BIS,-8,5);
        } else{
            $TLast_Number = 0; 
        }       
                            
        $lastNumber = $TLast_Number+1;      
        $pr_id      = sprintf("%05d", $lastNumber);        
        #$branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                            #->select('Nilai')
                                            #->get();
        
        $prefix_cabang = $this->getBranchCode();

        $padbln    = str_pad($bln,2,"0",STR_PAD_LEFT);

        $no_sp     = $type_nomor.$prefix_cabang.'/'.$thn.$padbln.'/'.$pr_id;                        
        return  $no_sp ; 
    }

    public function getNewBDNumber($type_nomor,$tanggal_transaksi)
    {
        $tanggal    = New Carbon($tanggal_transaksi);    
        $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
        $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;      

        $NoBD_BIS   = pc_barang_datang_model::select('No_BD')    
                                            ->whereRaw('MONTH(Tgl_BD) = ?',$bln)
                                            ->whereRaw('YEAR(Tgl_BD) = ?', $thn)                                
                                            ->orderBy('No_BD','desc')     
                                            ->limit('1')
                                            ->get();
                            
        if (count($NoBD_BIS)>0){                       
            $TLast_Number = substr($NoBD_BIS,-8,5);
        } else{
            $TLast_Number = 0; 
        }       
                            
        $lastNumber = $TLast_Number+1;      
        $pr_id      = sprintf("%05d", $lastNumber);
        #$branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                            #->select('Nilai')
                                            #->get();
        
        #$prefix_cabang = $branchCode[0]['Nilai'];
        $prefix_cabang = $this->getBranchCode();
        $padbln    = str_pad($bln,2,"0",STR_PAD_LEFT);

        $no_bd     = $type_nomor.$prefix_cabang.'/'.$thn.$padbln.'/'.$pr_id;                        
        return  $no_bd ; 
    }

    public function getNewFKNumber($type_nomor,$tanggal_transaksi)
    {
        $tanggal    = New Carbon($tanggal_transaksi);    
        $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
        $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;       

        $NoFK_BIS   = sl_faktur_model::select('No_Faktur')    
                                       ->whereRaw('MONTH(Tgl_Faktur) = ?',$bln)
                                       ->whereRaw('YEAR(Tgl_Faktur) = ?', $thn)                                
                                       ->orderBy('No_Faktur','desc')     
                                       ->limit('1')
                                       ->get();
                    
        if (count($NoFK_BIS)>0){                       
            $TLast_Number = substr($NoFK_BIS,-8,5);
        } else{
            $TLast_Number = 0; 
        }       
                            
        $lastNumber = $TLast_Number+1;      
        $pr_id      = sprintf("%05d", $lastNumber);
        #$branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                            #->select('Nilai')
                                            #->get();
        
        #$prefix_cabang = $branchCode[0]['Nilai'];
        $prefix_cabang = $this->getBranchCode();
        $padbln    = str_pad($bln,2,"0",STR_PAD_LEFT);

        $no_fk     = $type_nomor.$prefix_cabang.'/'.$thn.$padbln.'/'.$pr_id;                        
        return  $no_fk; 
    }


    public function getNewFCNumber($type_nomor,$tanggal_transaksi)
    {
        $tanggal    = New Carbon($tanggal_transaksi);    
        $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
        $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;       


        $NoFC_BIS   = sr_pemfakturan_model::select('No_Pemfakturan')    
                                            ->whereRaw('MONTH(Tanggal_Pemfakturan) = ?',$bln)
                                            ->whereRaw('YEAR(Tanggal_Pemfakturan) = ?', $thn)                                
                                            ->orderBy('No_Pemfakturan','desc')     
                                            ->limit('1')
                                            ->get();
                    
        if (count($NoFC_BIS)>0){                       
            $TLast_Number = substr($NoFC_BIS,-8,5);
        } else{
            $TLast_Number = 0; 
        }       
                            
        $lastNumber = $TLast_Number+1;      
        $pr_id      = sprintf("%05d", $lastNumber);
        #$branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                            #->select('Nilai')
                                            #->get();
        
        #$prefix_cabang = $branchCode[0]['Nilai'];
        $prefix_cabang = $this->getBranchCode();
        $padbln = str_pad($bln,2,"0",STR_PAD_LEFT);
        $no_fc  = $type_nomor.$prefix_cabang.'/'.$thn.$padbln.'/'.$pr_id;                        
        return  $no_fc ; 
    }

    public function getNewKJNumber($type_nomor,$tanggal_transaksi)
    {        
            $tanggal    = New Carbon($tanggal_transaksi);    
            $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
            $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;             
            
            $NoKJ_BIS   = StockOpname::select('no_kertas_kerja')    
                                      ->whereRaw('MONTH(Tanggal) = ?',$bln)
                                      ->whereRaw('YEAR(Tanggal) = ?',$thn)                                
                                      ->orderBy('no_kertas_kerja','desc')     
                                      ->limit('1')
                                      ->get();
    
            if (count($NoKJ_BIS)>0){                       
                $TLast_Number = substr($NoKJ_BIS,-8,5);
            } else{
                $TLast_Number = 0; 
            }       
        
            $lastNumber = $TLast_Number+1;      
            $pr_id      = sprintf("%05d", $lastNumber);
            #$branchCode = sy_konfigurasi_model::where('Item','nocabang')
            #                                    ->select('Nilai')
            #                                    ->get();
            #$prefix_kj = $branchCode[0]['Nilai'];
            $prefix_cabang = $this->getBranchCode();
            $padbln    = str_pad($bln,2,"0",STR_PAD_LEFT);

            $no_kj     = $type_nomor.$prefix_kj.'/'.$thn.$padbln.'/'.$pr_id;                        
            return  $no_kj ;                     
    }

    public function getNewDeliveryNumber($type_nomor,$tanggal_transaksi)
    {           
        $tanggal    = New Carbon($tanggal_transaksi);
            
        $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
        $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;             

        if($type_nomor=='DS'){    
            $No_DO      = in_delivery_model::select('no_delivery')    
                                            ->where('Jenis_referensi','=','SR')
                                            ->whereRaw('MONTH(Tgl_Delivery) = ?',$bln)
                                            ->whereRaw('YEAR(Tgl_Delivery) = ?', $thn)                                
                                            ->orderBy('no_delivery','desc')     
                                            ->limit('1')
                                            ->get();
        } else if($type_nomor=='DO'){   
            $No_DO      = in_delivery_model::select('no_delivery')    
                                            ->where('Jenis_referensi','=','SP')
                                            ->whereRaw('MONTH(Tgl_Delivery) = ?',$bln)
                                            ->whereRaw('YEAR(Tgl_Delivery) = ?', $thn)                                
                                            ->orderBy('no_delivery','desc')     
                                            ->limit('1')
                                            ->get();    
        } else if($type_nomor=='DM'){   
            $No_DO      = in_delivery_model::select('no_delivery')    
                                            ->where('Jenis_referensi','=','MC')
                                            ->whereRaw('MONTH(Tgl_Delivery) = ?',$bln)
                                            ->whereRaw('YEAR(Tgl_Delivery) = ?', $thn)                                
                                            ->orderBy('no_delivery','desc')     
                                            ->limit('1')
                                            ->get();    
        }
          
        if (count($No_DO)>0){                       
            $TLast_Number = substr($No_DO,-8,5);
        } else{
            $TLast_Number = 0; 
        }       
            $lastNumber = $TLast_Number+1;                  

            $pr_id = sprintf("%05d", $lastNumber);

            #$branchCode = sy_konfigurasi_model::where('Item','nocabang')
            #                                    ->select('Nilai')
            #                                    ->get();
            #$prefix_cabang = $branchCode[0]['Nilai'];
            $prefix_cabang = $this->getBranchCode();
            $padbln    = str_pad($bln,2,"0",STR_PAD_LEFT);

            $no_ds              = $type_nomor.$prefix_cabang.'/'.$thn.$padbln.'/'.$pr_id;            
            $Data['new_number'] = $no_ds ;
         
            return $no_ds ;      
            
    }

    public function getNewOCNumber($type_nomor,$tanggal_transaksi)
    {           
      
   
            $tanggal    = New Carbon($tanggal_transaksi);
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
            } else {
                $TLast_Number = 0; 
            }       
        
            $lastNumber = $TLast_Number+1;      

            $pr_id = sprintf("%05d", $lastNumber);


           # $branchCode = sy_konfigurasi_model::where('Item','nocabang')
           #                                     ->select('Nilai')
           #                                     ->get();
           # $prefix_cabang = $branchCode[0]['Nilai'];
            $prefix_cabang = $this->getBranchCode();

            $padbln    = str_pad($bln,2,"0",STR_PAD_LEFT);

            $no_oc=$type_nomor.$prefix_cabang.'/'.$thn.$padbln.'/'.$pr_id;
            $Data['new_number'] = $no_oc;           
            
            return  $no_oc ;             
                       
    }

    public function getNewKCNumber($type_nomor,$tanggal_transaksi)
    {                
            $tanggal    = New Carbon($tanggal_transaksi);
            #========================================================================
            $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
            $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;             
            #=========================================================================
            $No_Pengembalian = sr_pengembalian_model::select('No_Pengembalian')    
                                                      ->whereRaw('MONTH(Tanggal_Pelaporan) = ?',$bln)
                                                      ->whereRaw('YEAR(Tanggal_Pelaporan) = ?', $thn)                                
                                                      ->orderBy('No_Pengembalian','desc')     
                                                      ->limit('1')
                                                      ->get();             

            if (count($No_Pengembalian)>0){                       
                $TLast_Number = substr($No_Pengembalian,-8,5);
            } else {
                $TLast_Number = 0; 
            }       
        
            $lastNumber = $TLast_Number+1;      

            $pr_id = sprintf("%05d", $lastNumber);


            #$branchCode = sy_konfigurasi_model::where('Item','nocabang')
            #                                    ->select('Nilai')
            #                                    ->get();
            #$prefix_cabang = $branchCode[0]['Nilai'];
            $prefix_cabang = $this->getBranchCode();

            $padbln    = str_pad($bln,2,"0",STR_PAD_LEFT);

            $no_oc=$type_nomor.$prefix_cabang.'/'.$thn.$padbln.'/'.$pr_id;
            $Data['new_number'] = $no_oc;            
            return  $no_oc ;                       
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
        else {    
            response()->json(['opname_status'=>0])->send();       
        }       

    }
    

}
