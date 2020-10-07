<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\SequenceController;

class SpreadingController extends Controller
{
    protected $SequenceController;

    public function __construct(SequenceController $SequenceController)
    {
        $this->SequenceController = $SequenceController;
    }

    public function ucox(Request $request)
    {
        $data= $this->SequenceController->getNewOCNumber($request);
        return($data);
    }

    public function postPickingSpreading(Request $request)
    {
        $odoo   = new \Edujugon\Laradoo\Odoo();
        $odoo   = $odoo->connect();   

        $spreading_header     = [];
        $spreading_detail     = [];
        $spreading_subdetail  = [];        
        #$NomorSpreading       = $this->SequenceController->getNewOCNumber($request);


        return($request);

        //return($NomorSpreading);

        #return($this->BISMySQLController->getNewOCNumber('KJ'));

        // header      
        /*
        $spreading_header['No_Peminjaman']    = $No_Peminjaman;
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
            $spreading_subdetail[$row]['No_Peminjaman']  = $details[$row]['No_Peminjaman']; 
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
        }      
        response()->json(['opname_status'=>0])->send();   
        */
    }
   

    
}
