<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\SequenceController;
#use GuzzleHttp\ Request;
use GuzzleHttp\Client;



class SpreadingController extends Controller
{   
    protected $SequenceController;

    public function __construct(SequenceController $SequenceController)
    {
        $this->SequenceController = $SequenceController;
    }
 
 
    public function postPickingSpreading(Request $request) 
    {
        #$odoo   = new \Edujugon\Laradoo\Odoo();
        #$odoo   = $odoo->connect();   
        $spreading_header     = [];
        $spreading_detail     = [];
        $spreading_subdetail  = [];                

        $client = new Client();
        #$res = $client->request('POST', 'http://192.168.21.175/api/blg/postPickingSpreading/74'
        #);
        #return($request->tanggal_transaksi);
      #  return  $request;//->tanggal_transaksi . '  '.$request->type_nomor;

    

        $response = $client->request('POST', 'http://192.168.21.175/api/blg/getNewNumber', [
            'form_params' => [
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'type_nomor' => $request->type_nomor
            ]
        ]);
        

 
        #$nomor_oc             = $this->SequenceController->getNewNumber($request);        
        #$nomor_ds             = $this->SequenceController->getNewNumber($request);

        return  $response;
        #$data= $data1->toArray();               


        $new_number['oc_number'] = $nomor_oc['new_number']; 
        $new_number['ds_number'] = $nomor_ds['new_number']; 
        $new_number['status']    = 1; 
        $new_number['message']   = 'Data Peminjaman Berhasil di Transfer'; 
        $new_number['success']   = 1;
        $new_number['code']      = 200;

        #$Data=json_decode($NomorSpreading, True);        
        
        
       # return redirect('blg/postPickingSpreading/'.$request);
 /*
        response()->json([
                         'spreading'=>$spreading_header['No_Peminjaman'],
                         'ware_house_id'=>(int)$request->ware_house_id,
                         'id'=>(int)$request->id
                         ])->send(); */

        //return($NomorSpreading);

        #return($this->BISMySQLController->getNewOCNumber('KJ'));

        // header      
        /*
        $spreading_header['No_Peminjaman']    = $new_number['oc_number'];
        $spreading_header['ID_Spreading']     = $ID_Spreading;
        $spreading_header['Tanggal_Pinjam']   = $Tanggal_Pinjam;
        $spreading_header['Kode_Rayon']       = '';
        $spreading_header['Status_Tercetak']  = 'N';
        $spreading_header['User_ID']          = 'OdooWMS';
        $spreading_header['Time_Stamp']       = Carbon::now('Asia/Jakarta');   
        $spreading_header['No_Depo']          = '0';        
        
        $row  = 0; 
        foreach($result['spreading_detail'] as $details[]) 
        {
            $spreading_subdetail[$row]['No_Peminjaman']  = $new_number['oc_number'];
            $spreading_subdetail[$row]['Kode_Barang']    = $details[$row]['Kode_Barang']; 
            $spreading_subdetail[$row]['No_Detail']      = $details[$row]['No_Detail']; 
            $spreading_subdetail[$row]['Satuan']         = $details[$row]['Satuan'];             
            $spreading_subdetail[$row]['Jumlah']         = $details[$row]['Jumlah']; 
            $row++;    
        }     

        $row = 0; 
        foreach($result['spreading_subdetail'] as $subdetails[]) 
        {
            $spreading_subdetail[$row]['No_Peminjaman']  = $subdetails[$row]['No_Peminjaman']; 
            $spreading_subdetail[$row]['Kode_Barang']    = $subdetails[$row]['Kode_Barang']; 
            $spreading_subdetail[$row]['No_Detail']      = $subdetails[$row]['No_Detail']; 
            $spreading_subdetail[$row]['Satuan']         = $subdetails[$row]['Satuan']; 
            $spreading_subdetail[$row]['Kadaluarsa']     = $subdetails[$row]['Kadaluarsa']; 
            $spreading_subdetail[$row]['Jumlah']         = $subdetails[$row]['Jumlah']; 
            $row++;    
        }   */   
        response()->json([
           
            $new_number
            ])->send();   
    
    }
   

    
}
