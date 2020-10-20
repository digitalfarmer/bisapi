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
        if(!$tanggal_transaksi)
        {
            $tanggal_transaksi   = New Carbon($tanggal_transaksi);     
        }

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

    public function convertTgltoTahunBulan($tanggal_transaksi)
    {
        $tanggal    = New Carbon($tanggal_transaksi);    
        $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
        $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month; 

        $padbln     = str_pad($bln,2,"0",STR_PAD_LEFT);

        return $thn.$padbln;
    }

    public function getBranchCode()
    {
        $branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                            ->select('Nilai')
                                            ->get();
        $prefix_cabang = $branchCode[0]['Nilai'];

        return $prefix_cabang;    
    }

    public function getLastNumber($type_nomor,$table_name,$tanggal_transaksi,$number_field_name,$date_field_name)
    {
        $tanggal    = New Carbon($tanggal_transaksi);    
        $thn        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->year;
        $bln        = Carbon::createFromFormat('Y-m-d H:i:s', $tanggal)->month;            
         
        if($type_nomor=='DS')        
        {
            $Number = DB::table($table_name)->select($number_field_name)
                                                   ->whereRaw('MONTH('.$date_field_name.') =?',$bln)
                                                   ->whereRaw('YEAR('.$date_field_name.') =?',$thn)
                                                   ->where('Jenis_referensi','=','SR')
                                                   ->orderBy($number_field_name,'desc')     
                                                   ->limit('1')
                                                   ->get();                            
                                            
        } else if ($type_nomor=='DM') 
        {
            $Number = DB::table($table_name)->select($number_field_name)
                                                   ->whereRaw('MONTH('.$date_field_name.') =?',$bln)
                                                   ->whereRaw('YEAR('.$date_field_name.') =?',$thn)
                                                   ->where('Jenis_referensi','=','MC')
                                                   ->orderBy($number_field_name,'desc')     
                                                   ->limit('1')
                                                   ->get();                             

        } else if ($type_nomor=='DO') 
        {
            $Number = DB::table($table_name)->select($number_field_name)
                                                  ->whereRaw('MONTH('.$date_field_name.') =?',$bln)
                                                  ->where('Jenis_referensi','=','SP')
                                                  ->whereRaw('YEAR('.$date_field_name.') =?',$thn)
                                                  ->orderBy($number_field_name,'desc')     
                                                  ->limit('1')
                                                  ->get();      

        }  else
        {
            $Number = DB::table($table_name)->select($number_field_name)
                                                  ->whereRaw('MONTH('.$date_field_name.') =?',$bln)                                                  
                                                  ->whereRaw('YEAR('.$date_field_name.') =?',$thn)
                                                  ->orderBy($number_field_name,'desc')     
                                                  ->limit('1')
                                                  ->get();              
        }        

        if (count($Number)>0){                       
            $TLast_Number = substr($Number,-8,5);
        } else
        {
            $TLast_Number = 0; 
        }  
        $lastNumber = $TLast_Number+1;   
              
        return  sprintf("%05d", $lastNumber);
    }

     
    public function getNewSPNumber($type_nomor,$tanggal_transaksi)    
    {       
        $pr_id         = $this->getLastNumber($type_nomor,'sl_surat_pesanan',$tanggal_transaksi,'No_SP','Tgl_SP');              
        $prefix_cabang = $this->getBranchCode();        
        $tahun_bulan   = $this->convertTgltoTahunBulan($tanggal_transaksi);
        $no_sp         = $type_nomor.$prefix_cabang.'/'.$tahun_bulan.'/'.$pr_id;                               

        return $no_sp; 
    }

    public function getNewBDNumber($type_nomor,$tanggal_transaksi)
    {         
        $pr_id         = $this->getLastNumber($type_nomor,'pc_barang_datang',$tanggal_transaksi,'No_BD','Tgl_BD');              
        $prefix_cabang = $this->getBranchCode();     
        $tahun_bulan   = $this->convertTgltoTahunBulan($tanggal_transaksi);
        $no_bd         = $type_nomor.$prefix_cabang.'/'.$tahun_bulan.'/'.$pr_id; 

        return  $no_bd; 
    }

    public function getNewFKNumber($type_nomor,$tanggal_transaksi)
    {      
        $pr_id         = $this->getLastNumber($type_nomor,'sl_faktur',$tanggal_transaksi,'No_Faktur','Tgl_Faktur');              
        $prefix_cabang = $this->getBranchCode();
        $tahun_bulan   = $this->convertTgltoTahunBulan($tanggal_transaksi);
        $no_fk         = $type_nomor.$prefix_cabang.'/'.$tahun_bulan.'/'.$pr_id;   
        
        return  $no_fk; 
    }

    public function getNewFCNumber($type_nomor,$tanggal_transaksi)
    {         
        $pr_id         = $this->getLastNumber($type_nomor,'sr_pemfakturan',$tanggal_transaksi,'No_Pemfakturan','Tanggal_Pemfakturan');                
        $prefix_cabang = $this->getBranchCode();         
        $tahun_bulan   = $this->convertTgltoTahunBulan($tanggal_transaksi);
        $no_fc         = $type_nomor.$prefix_cabang.'/'.$tahun_bulan.'/'.$pr_id;                        
        
        return  $no_fc; 
    }

    public function getNewKJNumber($type_nomor,$tanggal_transaksi)
    {                
        $pr_id         = $this->getLastNumber($type_nomor,'in_stock_opname',$tanggal_transaksi,'No_Kertas_Kerja','Tanggal');                   
        $prefix_cabang = $this->getBranchCode();      
        $tahun_bulan   = $this->convertTgltoTahunBulan($tanggal_transaksi);      
        $no_kj         = $type_nomor.$prefix_cabang.'/'.$tahun_bulan.'/'.$pr_id;                        
        
        return  $no_kj;                     
    }

    public function getNewDeliveryNumber($type_nomor,$tanggal_transaksi)
    {                         
        $pr_id          = $this->getLastNumber($type_nomor,'in_delivery',$tanggal_transaksi,'No_Delivery','Tgl_Delivery');                   
        $prefix_cabang  = $this->getBranchCode();
        $tahun_bulan    = $this->convertTgltoTahunBulan($tanggal_transaksi);                  
        $nomor          = $type_nomor.$prefix_cabang.'/'.$tahun_bulan.'/'.$pr_id;                       
         
        return $nomor;   
    }

    public function getNewOCNumber($type_nomor,$tanggal_transaksi)
    {           
        $pr_id         = $this->getLastNumber($type_nomor,'sr_peminjaman',$tanggal_transaksi,'No_Peminjaman','Tanggal_Pinjam');                   
        $prefix_cabang = $this->getBranchCode();            
        $tahun_bulan   = $this->convertTgltoTahunBulan($tanggal_transaksi);      
        $no_oc         = $type_nomor.$prefix_cabang.'/'.$tahun_bulan.'/'.$pr_id;                               

        return  $no_oc;                                    
    }

    public function getNewKCNumber($type_nomor,$tanggal_transaksi)
    {                
        $pr_id         = $this->getLastNumber($type_nomor,'sr_pengembalian',$tanggal_transaksi,'No_Pengembalian','Tanggal_Pelaporan');                   
        $prefix_cabang = $this->getBranchCode();            
        $tahun_bulan   = $this->convertTgltoTahunBulan($tanggal_transaksi);      
        $no_kc         = $type_nomor.$prefix_cabang.'/'.$tahun_bulan.'/'.$pr_id;                   

        return  $no_kc;                       
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
