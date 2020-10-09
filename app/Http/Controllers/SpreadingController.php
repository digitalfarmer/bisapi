<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\in_delivery_model;
use App\in_delivery_detail_model;
use App\in_delivery_subdetail_model;
use App\Spreading\sr_peminjaman_model;
use App\Spreading\sr_peminjaman_detail_model;
use App\mapping\ms_odoo_map_uom_product_model;
use App\mapping\ms_mapping_wh_odoo_model;
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
        //Kalo Ambil dari request berbentuk Object
        #$odoo   = new \Edujugon\Laradoo\Odoo();
        #$odoo   = $odoo->connect();  

        $data   = $request->all();

        #return $data['spreading_header']['date'];
        //Kalo Ambil dari sini berbentuk Array

        
        #return $Peminjaman_Detail;

        $warehouse_code = $data['spreading_header']['warehouse_code'];
        #return  $warehouse_code;        
        
        $Picking_ID         = $request->picking_id;
        #OC
        $Peminjaman_Header  = [];
        $Peminjaman_Detail  = [];        
        
        #DS
        $Delivery_Header    = [];
        $Delivery_Detail    = [];
        $Delivery_Subdetail = [];
        
        $nomor_oc = $this->SequenceController->getNewOCNumber($data['spreading_header']['date']);
        $nomor_ds = $this->SequenceController->getNewDeliveryNumber('DS',$data['spreading_header']['date']);                    
        #return $nomor_ds;        

        $Peminjaman_Header['No_Peminjaman']   = $nomor_oc;         
        $Peminjaman_Header['ID_Spreading']    = '';         
        $Peminjaman_Header['Tanggal_Pinjam']  = $data['spreading_header']['date'];                                 
        $Peminjaman_Header['Kode_Rayon']      = '';                                 
        $Peminjaman_Header['Status_Tercetak'] = 'N';                                 
        $Peminjaman_Header['Time_Stamp']      = Carbon::now('Asia/Jakarta');                             
        $Peminjaman_Header['User_ID']         = 'OdooWMS';                                 
        $Peminjaman_Header['No_Depo']         = '0';                                         
                
        $Delivery_Header['No_Delivery']         = $nomor_ds;            
        $Delivery_Header['Kode_Referensi']      = $nomor_oc;
        $Delivery_Header['Jenis_referensi']     = 'SR';                   
        $Delivery_Header['Tgl_Delivery']        = $data['spreading_header']['date'];       
        $Delivery_Header['Tgl_Permintaan_Kirim']= $data['spreading_header']['date'];  
        $Delivery_Header['Nama_Tujuan']         = '';  //83320021  IMAN NASRULOH     
       # $Delivery_Header['Alamat_Tujuan']       = '';        
        #$Delivery_Header['Kota_Tujuan']         = '';                  
        $Delivery_Header['Time_Stamp']          = Carbon::now('Asia/Jakarta');
        $Delivery_Header['User_ID']             = 'OdooWMS';              
        $Delivery_Header['Status_Tercetak']     = 'N';   
            
        $row = 0; 
        #$row++;
        foreach($data['spreading_detail'] as $Details[]) 
        {
            $Peminjaman_Detail[$row]['No_Peminjaman']   = $nomor_oc;
            $satuan = ms_odoo_map_uom_product_model::where('product_id',$Details[$row]['product_id']) 
                                                    ->where('uom_id',$Details[$row]['uom_id'])
                                                    ->select('uom_long_name')
                                                    ->get();
            #return $satuan;

            $Peminjaman_Detail[$row]['Kode_Barang']     = $Details[$row]['product_code']; 
            $Peminjaman_Detail[$row]['No_Detail']       = $row+1;
            $Peminjaman_Detail[$row]['Satuan']          = $satuan[0]['uom_long_name'];             
            $Peminjaman_Detail[$row]['Jumlah']          = $Details[$row]['qty']; 
            $Delivery_Detail[$row]['No_Delivery']       = $nomor_ds;
            $Delivery_Detail[$row]['Kode_Barang']       = $Details[$row]['product_code'];       
            $Delivery_Detail[$row]['Jumlah']            = $Details[$row]['qty'];                      
            $Delivery_Detail[$row]['Satuan']            = $satuan[0]['uom_long_name'];  
            if($row==0) {                                 
               $Delivery_Detail[$row]['Prepared']          = 'Y';                                             
            } else {
               $Delivery_Detail[$row]['Prepared']          = 'N';                                                 
            }
            $Delivery_Detail[$row]['ID_Program_Promosi']= '';//$Details[$row]['ID_Program_Promosi'];                                  

            $row++;    
        }     

        $row = 0;         
        foreach($data['spreading_subdetail'] as $Subdetails[]) 
        {
            $Delivery_Subdetail[$row]['No_Delivery'] = $nomor_ds; 
            $Mapping_Kode_Gudang          = ms_mapping_wh_odoo_model::where('wh_code','=',$warehouse_code)
                                                                     ->select('kode_gudang')
                                                                     ->get();

            $Delivery_Subdetail[$row]['Kode_Gudang'] = $Mapping_Kode_Gudang[0]['kode_gudang']; 
            $Delivery_Subdetail[$row]['Kode_Barang'] = $Subdetails[$row]['product_code'];             
            $Delivery_Subdetail[$row]['No_Batch']    = $Subdetails[$row]['lot_name']; 
            $Delivery_Subdetail[$row]['Jumlah']      = $Subdetails[$row]['qty']; 

            $satuan = ms_odoo_map_uom_product_model::where('product_id',$Subdetails[$row]['product_id']) 
                                                    ->where('uom_id',$Subdetails[$row]['uom_id'])
                                                    ->select('uom_long_name')
                                                    ->get();

            $Delivery_Subdetail[$row]['Satuan']      = $satuan[0]['uom_long_name']; 
            $Delivery_Subdetail[$row]['Kadaluarsa']  = $Subdetails[$row]['expired']; 
            $Delivery_Subdetail[$row]['Terima']      = $Subdetails[$row]['qty']; 
            #$Delivery_Subdetail[$row]['ID_Program_Promosi'] = $Subdetails[$row]['ID_Program_Promosi'];                                    
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
                             'No_Peminjaman'=>$nomor_oc,
                             'No_Delivery'=>$nomor_ds,             
                             'success'=>1,
                             'code'=>200,                                   
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

