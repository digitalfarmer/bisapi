<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SpreadingController extends Controller
{   
    protected $SequenceController;

    public function __construct(SequenceController $SequenceController)
    {
        $this->SequenceController = $SequenceController;
    } 
 
    public function postPickingSpreading(Request $request) 
    {
        #$odoo                = new \Edujugon\Laradoo\Odoo();
        #$odoo                = $odoo->connect();  

        $spreading_header     = [];
        $spreading_detail     = [];
        $spreading_subdetail  = [];                
        
        $nomor_oc = $this->SequenceController->getNewOCNumber($request->tanggal_transaksi);
        $nomor_ds = $this->SequenceController->getNewDeliveryNumber('DS',$request->tanggal_transaksi);                  
  
        
        $Peminjaman_Header['No_Peminjaman']   = $nomor_oc;         
        $Peminjaman_Header['ID_Spreading']    = '';         
        $Peminjaman_Header['Tgl_Peminjaman']  = $request->tanggal_transaksi;                                 
        $Peminjaman_Header['Kode_Rayon']      = '';                                 
        $Peminjaman_Header['Status_Tercetak'] = 'N';                                 
        $Peminjaman_Header['Time_Stamp']      = Carbon::now('Asia/Jakarta');                             
        $Peminjaman_Header['User_ID']         = 'OdooWMS';                                 
        $Peminjaman_Header['No_Depo']         = '0';                                         
                
        $Delivery_Header['No_Delivery']          = $nomor_ds;            
        $Delivery_Header['Kode_Referensi']       = $nomor_oc;
        $Delivery_Header['Jenis_referensi']      = 'DS';                   
        $Delivery_Header['Tgl_Delivery']         = $request->tanggal_transaksi;       
        $Delivery_Header['Tgl_Permintaan_Kirim'] = '';
        $Delivery_Header['Nama_Tujuan']          = '';       
        $Delivery_Header['Alamat_Tujuan']        = '';        
        $Delivery_Header['Kota_Tujuan']          = '';                  
        $Delivery_Header['Time_Stamp']           = Carbon::now('Asia/Jakarta');
        $Delivery_Header['User_ID']              = 'OdooWMS';              
        $Delivery_Header['Status_Tercetak']      = 'N';   

        $new_number['message']        = 'Data Peminjaman Berhasil di Transfer'; 
        $new_number['success']        = 1;        
        
 /*
        $spreading_header['No_Peminjaman']    = $nomor_oc;
        $spreading_header['ID_Spreading']     = 1;
        $spreading_header['Tanggal_Pinjam']   = $request->tanggal_transaksi;
        $spreading_header['Kode_Rayon']       = '';
        $spreading_header['Status_Tercetak']  = 'N';
        $spreading_header['User_ID']          = 'OdooWMS';
        $spreading_header['Time_Stamp']       = Carbon::now('Asia/Jakarta');   
        $spreading_header['No_Depo']          = '0';        
        
        $row  = 0; 
        foreach($result['spreading_detail'] as $details[]) 
        {
            $spreading_subdetail[$row]['No_Peminjaman']  = $nomor_oc;
            $spreading_subdetail[$row]['Kode_Barang']    = $details[$row]['Kode_Barang']; 
            $spreading_subdetail[$row]['No_Detail']      = $details[$row]['No_Detail']; 
            $spreading_subdetail[$row]['Satuan']         = $details[$row]['Satuan'];             
            $spreading_subdetail[$row]['Jumlah']         = $details[$row]['Jumlah']; 
            $row++;    
        }     

        $row = 0; 
        foreach($result['spreading_subdetail'] as $subdetails[]) 
        {
            $spreading_subdetail[$row]['No_Delivery']    = $nomor_ds; 
            $spreading_subdetail[$row]['Kode_Barang']    = $subdetails[$row]['Kode_Barang']; 
            $spreading_subdetail[$row]['No_Detail']      = $subdetails[$row]['No_Detail']; 
            $spreading_subdetail[$row]['Satuan']         = $subdetails[$row]['Satuan']; 
            $spreading_subdetail[$row]['Kadaluarsa']     = $subdetails[$row]['Kadaluarsa']; 
            $spreading_subdetail[$row]['Jumlah']         = $subdetails[$row]['Jumlah']; 
            $row++;    
        }   
        */

        response()->json(['Peminjaman_Header'=>$Peminjaman_Header,'DO_Peminjaman_Header'=>$Delivery_Header])->send();       
   }   
    
}
