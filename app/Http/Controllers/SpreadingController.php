<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\in_delivery_model;
use App\in_delivery_detail_model;
use App\in_delivery_subdetail_model;
use App\sr_peminjaman_model;
use App\sr_peminjaman_detail_model;
use Illuminate\Support\Facades\DB;

class SpreadingController extends Controller
{   
    protected $SequenceController;

    public function __construct(SequenceController $SequenceController)
    {
        $this->SequenceController = $SequenceController;
    } 
 
    public function postPickingSpreading(Request $request) 
    {
        $odoo   = new \Edujugon\Laradoo\Odoo();
        $odoo   = $odoo->connect();  

        $Picking_ID         = $request->picking_id;
        #OC
        $Peminjaman_Header  = [];
        $Peminjaman_Detail  = [];        
        
        #DS
        $Delivery_Header    = [];
        $Delivery_Detail    = [];
        $Delivery_Subdetail = [];
        
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
        $Delivery_Header['Tgl_Permintaan_Kirim'] = $request->tanggal_transaksi;  
        $Delivery_Header['Nama_Tujuan']          = '';  //83320021  IMAN NASRULOH     
        $Delivery_Header['Alamat_Tujuan']        = '';        
        $Delivery_Header['Kota_Tujuan']          = '';                  
        $Delivery_Header['Time_Stamp']           = Carbon::now('Asia/Jakarta');
        $Delivery_Header['User_ID']              = 'OdooWMS';              
        $Delivery_Header['Status_Tercetak']      = 'N';   
            
        $row  = 1; 
        foreach($result['spreading_detail'] as $Details[]) 
        {
            $Peminjaman_Detail[$row]['No_Peminjaman']   = $nomor_oc;
            $Peminjaman_Detail[$row]['Kode_Barang']     = $Details[$row]['Kode_Barang']; 
            $Peminjaman_Detail[$row]['No_Detail']       = $row;
            $Peminjaman_Detail[$row]['Satuan']          = $Details[$row]['Satuan'];             
            $Peminjaman_Detail[$row]['Jumlah']          = $Details[$row]['Jumlah']; 

            $Delivery_Detail[$row]['No_Delivery']       = $nomor_ds;
            $Delivery_Detail[$row]['Kode_Barang']       = $Details[$row]['Kode_Barang'];         
            $Delivery_Detail[$row]['Jumlah']            = $Details[$row]['Jumlah'];                      
            $Delivery_Detail[$row]['Satuan']            = $Details[$row]['Satuan'];                                  
            $Delivery_Detail[$row]['Prepared']          = $Details[$row]['Prepared'];                                             
            $Delivery_Detail[$row]['ID_Program_Promosi']= $Details[$row]['ID_Program_Promosi'];                                  

            $row++;    
        }     

        $row = 1; 
        foreach($result['spreading_subdetail'] as $Subdetails[]) 
        {
            $Delivery_Subdetail[$row]['No_Delivery'] = $nomor_ds; 
            $Delivery_Subdetail[$row]['Kode_Gudang'] = $Subdetails[$row]['Kode_Gudang']; 
            $Delivery_Subdetail[$row]['Kode_Barang'] = $Subdetails[$row]['Kode_Barang'];             
            $Delivery_Subdetail[$row]['No_Batch']    = $Subdetails[$row]['No_Batch']; 
            $Delivery_Subdetail[$row]['Jumlah']      = $Subdetails[$row]['Jumlah']; 
            $Delivery_Subdetail[$row]['Satuan']      = $Subdetails[$row]['Satuan']; 
            $Delivery_Subdetail[$row]['Kadaluarsa']  = $Subdetails[$row]['Kadaluarsa']; 
            $Delivery_Subdetail[$row]['Terima']      = $Subdetails[$row]['Jumlah']; 
            $Delivery_Subdetail[$row]['ID_Program_Promosi'] = $Subdetails[$row]['ID_Program_Promosi'];                        
            
            $row++;    
        }   
        
        try 
        {  
            DB::beginTransaction();
            // Transaction Peminjaman (OC)
            $Saved_Peminjaman_Header  = sr_peminjaman_model::insert($Peminjaman_Header);            
            $Saved_Peminjaman_Detail  = sr_peminjaman_detail_model::insert($Peminjaman_Detail);    

            // Transaction DO Peminjaman (DS)
            $Saved_Delivery_Header    = in_delivery_model::insert($Delivery_Header);                        
            $Saved_Delivery_Detail    = in_delivery_detail_model::insert($Delivery_Detail);
            $Saved_Delivery_Subdetail = in_delivery_subdetail_model::insert($Delivery_Subdetail);                                

            response()->json([
                             'success'=>1,
                             'code'=>200,
                             'No_Peminjaman'=>$nomor_oc,
                             'No_Delivery'=>$nomor_ds,                                                
                             'message'=>'Peminjaman Berhasil dibuat di BISMySQL !'
                            ])->send();    

          // Jika Table table diatas Berhasil di Insert
          // Maka Simpan Semua Datanya, Kommat Kommit
           DB::commit();                             
        } catch(\Exception $e)
        {
           // Jika ada error / Salah Satu Model Gagal di insert 
           // Maka Rollback, Semua data di batalkan (Tidak jadi di Insert)
           // Berlaku untuk model yang ada di Transaction
           DB::rollback();
           response()->json([
                            'success'=>0,
                            'code'=>400,
                            'message'=>'Peminjaman Dengan Referensi Picking ID : '.$Picking_ID.' Gagal di Proses ! '
                            ])->send(); 
           exit;
        }                                           
   }       
}
