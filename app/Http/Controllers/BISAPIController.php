<?php
namespace App\Http\Controllers;
 
use App\in_delivery_model;
use App\in_delivery_detail_model;
use App\in_delivery_subdetail_model;
use App\sl_surat_pesanan_model;
use App\sl_surat_pesanan_detail_model;
use App\ms_odoo_map_uom_product_model;
use App\in_delivery_flag_wms_model;
use App\ms_barang_satuan_model;
use App\in_stock_opname_model;
use App\in_stock_opname_selisih_model;
use App\DB;
use Session;

use Illuminate\Http\Request;

class BISAPIController extends Controller
{
  
   // public function getFromAPI(request $request)
   // {
      //  $no_delivery = $request->no_delivery;
        /*
        $no_delivery = $request->no_delivery;
        $urlAPI='http://192.168.21.175/api/bismysql/getDeliveryRow/'.$no_delivery;

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',  $urlAPI);
        */
      //  return response()->json(['data'=>$no_delivery]);

      //   }

    public function kartuStok(request $request)
    {      
        // /KJBLG20200800001
        
        $no_ref = substr($request->no_kertas_kerja,0,5).'/'.substr($request->no_kertas_kerja,5,6).'/'.substr($request->no_kertas_kerja,11,5);
                                    
        $opname = in_stock_opname_model::where('in_stock_opname.no_kertas_kerja',$no_ref) 
                                        ->join('in_stock_opname_selisih','in_stock_opname.no_kertas_kerja','=','in_stock_opname_selisih.no_kertas_kerja')
                                        ->join('ms_barang_satuan', function($join)
                                                        {
                                                            $join->on('ms_barang_satuan.kode_barang','=','in_stock_opname_selisih.kode_barang');
                                                            $join->on('ms_barang_satuan.level','=','in_stock_opname_selisih.level');
                                                        } 
                                        )                                                   
                                        ->select(
                                        #'in_stock_opname.Status_Barang',  
                                        #'in_stock_opname.Status',
                                        #'in_stock_opname.User_ID',           
                                        #'in_stock_opname.Time_Stamp',   
                                        //  'in_stock_opname_selisih.Jumlah',  
                                        'in_stock_opname.No_Kertas_Kerja',     
                                        'in_stock_opname.Kode_Principal',  
                                        'in_stock_opname.Kode_Divisi_Produk',  
                                        'in_stock_opname.Kode_Gudang',  
                                        'in_stock_opname.Tanggal',                                                                                  
                                        'in_stock_opname_selisih.No_Kertas_Kerja',     
                                        'in_stock_opname_selisih.Kode_Barang',  
                                        'in_stock_opname_selisih.No_Batch',    
                                        'in_stock_opname_selisih.Kadaluarsa',   
                                        'in_stock_opname_selisih.Level',                                        
                                        'ms_barang_satuan.Satuan',
                                        'in_stock_opname_selisih.Referensi'          
                                        )
                                        //so.no_kertas_kerja, so.kode_gudang, iso.kode_barang, iso.no_batch, 
                                        //so.status_barang, iso.kadaluarsa, iso.level'
                                        //->raw('sum(in_stock_opname_selisih.Jumlah) as Jumlah')                                        
                                        ->groupBy('in_stock_opname.No_Kertas_Kerja',     
                                        'in_stock_opname.Kode_Principal',  
                                        'in_stock_opname.Kode_Divisi_Produk',  
                                        'in_stock_opname.Kode_Gudang',  
                                        'in_stock_opname.Tanggal',                                                                                  
                                        'in_stock_opname_selisih.No_Kertas_Kerja',     
                                        'in_stock_opname_selisih.Kode_Barang',  
                                        'in_stock_opname_selisih.No_Batch',    
                                        'in_stock_opname_selisih.Kadaluarsa',   
                                        'in_stock_opname_selisih.Level',
                                        'ms_barang_satuan.Satuan',
                                        'in_stock_opname_selisih.Referensi') 
                                         ->get();
        
        response()->json([ 
                        'success'=>1,                       
                        'data'=>$opname,
                        'nomor'=>$no_ref                        
                        ])->send();   
                                                
                                                 
  
        # 'insert into in_kartu_stok_detail '.             
        /*
          $stock_opname2 = select(DB::raw(                     
            'select  '.
            'date_format(so.tanggal,"%Y%m"),  '.
            'so.no_kertas_kerja,  '.
            '"ADJUSTMENT",  '.
            'so.tanggal, '.
            'iso.kode_barang,  '.
            'so.kode_gudang,  '.
            'case so.status_barang '.
            'when "BAIK" then "AVAILABLE" '.
            'when "SEMI RUSAK" then "DEFECT" '.
            'when "RUSAK" then "REJECT" '.
            'when "BERMASALAH" then "PENDING" '.
            'when "HOLD" then "HOLD" '.
            'end,'.
            'iso.no_batch,'.
            'iso.level,  '.
            'sum(iso.jumlah),  '.
            'mbs.harga_beli,  '.
            '"LAINLAIN",'+
            '"", '.
            'iso.kadaluarsa, '.
            'so.time_stamp '.
            'from '.

            'in_stock_opname so,'.
            'in_stock_opname_selisih iso,'.
            'ms_barang_satuan mbs '.

            'where  '.
            'so.no_kertas_kerja="'.$no_ref.'" '.
            'and iso.no_kertas_kerja=so.no_kertas_kerja '.
            'and mbs.kode_barang=iso.kode_barang '.
            'and mbs.level=iso.level '.
            'group by so.no_kertas_kerja, so.kode_gudang, iso.kode_barang,'.
            'iso.no_batch, so.status_barang, iso.kadaluarsa, iso.level';     
            */   

    }

    public function getSessionID()
    {
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();    
             
        $session_id = Session::getId();
        $username   = $odoo->getUserName();
        $uid        = $odoo->getUid();
        $db         = $odoo->getdb();

        $sesi['session_id']  =$session_id;
        $sesi['user'] = $username;
        $sesi['uid']  = $uid;
        $sesi['db']   = $db;

        $no_delivery  = $odoo->Where('picking_type_id','in',[2,3])
                             ->where('state','=','assigned')
                          //->orWhere('picking_type_id','=',3)
                             ->fields('origin','name','picking_type_id')
                          //->limit(1)                             
                            ->get('stock.picking'); 

        #$odoo_session = $odoo->getUid();     

        if (count($no_delivery)>0) { 
            response()->json([ 
                            'success'=>1,                       
                            'data'=>$no_delivery,
                            'sesi'=>$sesi
                            ])->send();   
        }
        else
        {
            response()->json([          
                            'success'=>0,        
                            'sesi'=>$sesi
                            ])->send();       
        }    
                      
    }
    
    public function getStockMoveLineRow(request $request )
    {        
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();     
        
        $picking_id=$request->picking_id;
        try
        {
                $no_delivery = $odoo->where('id','=',$picking_id)
                                    ->where('state','=','assigned')
                                  //->orWhere('picking_type_id','=',2)
                                  //->orWhere('picking_type_id','=',3)
                                    ->fields('origin','name')
                                    ->limit(1)                             
                                    ->get('stock.picking'); 
                
                $c_data_delivery=count($no_delivery);       
                if($c_data_delivery<=0){        
                     //$res['success']=0;    
                     //$res['code']=400;            
                     response()->json(['success'=>0,
                                       'code'=>400,
                                       'message'=>'No Delivery berdasarkan Picking ID : '.$picking_id.' Tidak ada di BISMySQL !'])->send(); 
                     exit;   
                 }    
                
                $stock_move_line = $odoo->where('picking_id.id','=',$picking_id)   
                                        ->get('stock.move.line');

                $c_stock_move_line=count($stock_move_line);
                if($c_stock_move_line<=0){       
                   // $res['success']=0;    
                    //$res['code']=400;                 
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'Tidak Ada Item di stock move line !'])->send(); 
                    exit;   
                }    
                             
               
                $no_sp = in_delivery_model::where('no_delivery',$no_delivery[0]['origin'])
                                           ->select('kode_referensi')
                                           ->get();                                     
                $c_no_sp = count($no_sp);           
                
                
                if($c_no_sp<=0){        
                    //$res['success']=0;    
                    //$res['code']=400;                  
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'No SP untuk DO '.$no_delivery.' Tidak Ada !'])->send(); 
                    exit;   
                }                                     
                                    
                                          
                if( ($c_data_delivery>0) && ($c_stock_move_line>0) && ($c_no_sp>0) ) { 
                        $rowCount=0;
                        
                        //delete in_delivery_subdetail row first base on no_delivery                         
                      //  $data_delete = in_delivery_subdetail_model::where('no_delivery',  $no_delivery[0]['origin'])                                             
                      //                                            ->delete();
                      
                         /*
                        if(!$data_delete){
                            $res['success']=0;    
                            $res['code']=400;                           
                            $resVal=$res;   
                        }                     
                        */    

                        foreach ($stock_move_line as  $details)
                        {
                            $Do_Detail[$rowCount]['No_Delivery']   = $no_delivery[0]['origin'];
                            $Do_Detail[$rowCount]['Kode_Gudang']   = 'GDG01';                  
                            
                            $product = $odoo->where('id','=',$details['product_id'][0])                             
                                            ->fields('default_code','barcode')
                                            ->limit(1)                             
                                            ->get('product.product');

                            $c_product = count($product);   
                            
                            if($c_product<=0){    
                               // $res['success']=0;    
                               // $res['code']=400;                 
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Product ID '.$details['product_id'][0].' Tidak Ada !'])->send(); 
                                exit;   
                            }   

                            $batch = $odoo->where('id','=',$details['lot_id'][0])                             
                                          ->fields('name','expiry_date')
                                          ->limit(1)                             
                                          ->get('stock.production.lot');     
                                          
                            $c_batch = count($batch);   
                            
                            if($c_batch<=0){                                     
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Lot ID '.$details['lot_id'][0].' Tidak Ada !'])->send(); 
                                exit;   
                            }   
              
                            

                            $Do_Detail[$rowCount]['Kode_Barang'] = $product[0]['default_code'];
                            $Do_Detail[$rowCount]['No_Batch']    = $batch[0]['name'];
                            $Do_Detail[$rowCount]['Jumlah']      = $details['qty_done'];              
                      
                            $uom_long_name =  $odoo->where('id','=',$details['product_uom_id'][0])                             
                                                   ->fields('description')
                                                   ->limit(1)                             
                                                   ->get('uom.uom');         
                                                   
                            $c_uom_long_name = count($uom_long_name);   
                            
                            if($c_uom_long_name<=0){                    
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Produk UOM ID '.$details['product_uom_id'][0].' Tidak ada di BISMySQl !'])->send(); 
                                exit;   
                            }                                                               
                            
            
                            $Do_Detail[$rowCount]['Satuan']      = $uom_long_name[0]['description']; // need long uom name
                            $Do_Detail[$rowCount]['Kadaluarsa']  = $batch[0]['expiry_date'];
                            $Do_Detail[$rowCount]['Terima']      = $details['qty_done'];
                        
                            $program_promosi = sl_surat_pesanan_detail_model::where('No_SP','=',$no_sp[0]['kode_referensi'])
                                             ->where('Kode_Barang','=',$Do_Detail[$rowCount]['Kode_Barang']) 
                                             ->where('Satuan','=',$Do_Detail[$rowCount]['Satuan'])                                    
                                             ->limit(1) 
                                             ->get('ID_Program_Promosi');    
                            
                            $c_program_promosi = count($program_promosi);  
                            
                                                
                            if($c_program_promosi>0) 
                            {
                                $Do_Detail[$rowCount]['ID_Program_Promosi']= $program_promosi[0]['ID_Program_Promosi'];
                            }
                            else
                            {
                                $Do_Detail[$rowCount]['ID_Program_Promosi']='';
                            }        
                           
                            $rowCount++;   
                        } 

                         //then replace with new Record from Odoo
                        $is_saved=in_delivery_subdetail_model::insert($Do_Detail);
                        if($is_saved)
                        {   
                            $mapping['No_Delivery'] =$no_delivery[0]['origin'];   
                            $mapping['Flag_WMS']    ='Picked';                                                                                                                                                                                                                                                                                                
                            $mapping['picking_name']=$no_delivery[0]['name'];   
                            
                            in_delivery_flag_wms_model::where('no_delivery',$mapping['No_Delivery'])
                                                      ->update([
                                                      'Flag_WMS'=>'Picked'         
                                                      ]);
           
                            //$res['success']=1;                          
                            //$res['code']=200;                          
                            //$resVal=$res;
                            response()->json(['success'=>1,
                                              'code'=>200,
                                              'message'=>'Delivery Item '.$mapping['No_Delivery'].' Berhasil di kirim ke BISMySQL !',
                                              'data'=>$Do_Detail])->send(); 
                            exit;
                        }  
                        else
                        {
                                                   
                            //$resVal=$res;
                            response()->json(['success'=>0,
                                              'code'=>400,
                                              'message'=>'Delivery Item '.$mapping['No_Delivery'].' Gagal dikirim !'])->send(); 
                            exit;
                        }                     
                }
                else 
                {
               //     $res['success']=0;    
               //     $res['code']   =400;                           
               //     $resVal=$res;
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'Data Stok Picking Tidak Valid !'])->send(); 
                    exit;
                }        
        }
        catch (Exception $e) 
        {
           // $res['success']=0;    
           // $res['code']   =400;                         
            //$resVal=$res;    
            response()->json(['success'=>0,
                              'code'=>400,
                              'message'=>'Data Stock Picking Tidak Valid !'])->send(); 
            exit;        
        }
        //return response()->json([$resVal]);        
    }
    
    public function sentStockAdjusment(request $request )
    {
        #$odoo = new \Edujugon\Laradoo\Odoo();
        #$odoo = $odoo->connect();       

        #file_get_contents()

        //$adjustment_id = $request->id;
        foreach ($request as  $item_adjustment)
        {
            
        }

    }
 
    public function getUpdatePickingItem(request $request )
    {        
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();       

        $picking_id=$request->picking_id;
        try
        {
                $no_delivery = $odoo->where('id','=',$picking_id)
                                    ->where('state','=','assigned')
                                    ->Where('picking_type_id','in',[2,3])
                                   //->orWhere('picking_type_id','=',3)
                                    ->fields('origin','name')
                                    ->limit(1)                             
                                    ->get('stock.picking'); 
                
                $c_data_delivery=count($no_delivery);       
                if($c_data_delivery<=0){        
                     //$res['success']=0;    
                     //$res['code']=400;            
                     response()->json(['success'=>0,
                                       'code'=>400,
                                       'message'=>'No Delivery berdasarkan Picking ID : '.$picking_id.' Tidak ada di BISMySQL !'])->send(); 
                     exit;   
                 }    
                
                $stock_move_line = $odoo->where('picking_id.id','=',$picking_id)   
                                        ->get('stock.move.line');

                $c_stock_move_line=count($stock_move_line);
                if($c_stock_move_line<=0){       
                   // $res['success']=0;    
                    //$res['code']=400;                 
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'Tidak Ada Item di stock move line !'])->send(); 
                    exit;   
                }    
                             
               
                $no_sp = in_delivery_model::where('no_delivery',$no_delivery[0]['origin'])
                                           ->select('kode_referensi')
                                           ->get(); 
                                    
                $c_no_sp = count($no_sp);           
                
                
                if($c_no_sp<=0){        
                    //$res['success']=0;    
                    //$res['code']=400;                  
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'No SP untuk DO '.$no_delivery.' Tidak Ada !'])->send(); 
                    exit;   
                }                                     
                                    
                                          
                if( ($c_data_delivery>0) && ($c_stock_move_line>0) && ($c_no_sp>0) ) { 
                        $rowCount=0;
                        
                        //delete in_delivery_subdetail row first base on no_delivery                         
                        $data_delete = in_delivery_subdetail_model::where('no_delivery',  $no_delivery[0]['origin'])                                             
                                                                  ->delete();
                      
                         /*
                        if(!$data_delete){
                            $res['success']=0;    
                            $res['code']=400;                           
                            $resVal=$res;   
                        }                     
                        */    

                        foreach ($stock_move_line as  $details)
                        {
                            $Do_Detail[$rowCount]['No_Delivery']   = $no_delivery[0]['origin'];
                            $Do_Detail[$rowCount]['Kode_Gudang']   = 'GDG01';                  
                            
                            $product = $odoo->where('id','=',$details['product_id'][0])                             
                                            ->fields('default_code','barcode')
                                            ->limit(1)                             
                                            ->get('product.product');

                            $c_product = count($product);   
                            
                            if($c_product<=0){    
                               // $res['success']=0;    
                               // $res['code']=400;                 
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Product ID '.$details['product_id'][0].' Tidak Ada !'])->send(); 
                                exit;   
                            }   

                            $batch = $odoo->where('id','=',$details['lot_id'][0])                             
                                          ->fields('name','expiry_date')
                                          ->limit(1)                             
                                          ->get('stock.production.lot');     
                                          
                            $c_batch = count($batch);   
                            
                            if($c_batch<=0){                                     
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Lot ID '.$details['lot_id'][0].' Tidak Ada !'])->send(); 
                                exit;   
                            }                                          

                            $Do_Detail[$rowCount]['Kode_Barang']  = $product[0]['default_code'];
                            $Do_Detail[$rowCount]['No_Batch']     = $batch[0]['name'];
                            /*
                            $qty_done1    = $details['level1_qty']; 
                            $qty_done2    = $details['level2_qty']; 
                            $qty_done3    = $details['level3_qty']; 
                            $qty_done4    = $details['level4_qty'];
                            */
                            $qty_done     = $details['qty_done']; 
                            /*
                            if ($qty_done1>0)
                            {
                                $qty_done=$qty_done1;
                                $satuan =    ms_barang_satuan_model::where('Kode_Barang',$product[0]['default_code'])
                                                              ->where('Level',1)
                                                              ->select('Satuan')
                                                              ->get(); 
                            }
                            elseif ($qty_done2>0)
                            {
                                $qty_done=$qty_done2;  
                                $satuan =    ms_barang_satuan_model::where('Kode_Barang',$product[0]['default_code'])
                                                                ->where('Level',2)
                                                                ->select('Satuan')
                                                                ->get(); 
                            }
                            elseif ($qty_done3>0)
                            {
                                $qty_done=$qty_done3;  
                                $satuan =    ms_barang_satuan_model::where('Kode_Barang',$product[0]['default_code'])
                                                                ->where('Level',3)
                                                                ->select('Satuan')
                                                                ->get(); 
                            }
                            elseif ($qty_done4>0)
                            {
                                $qty_done=$qty_done4;  
                                $satuan =    ms_barang_satuan_model::where('Kode_Barang',$product[0]['default_code'])
                                                                ->where('Level',4)
                                                                ->select('Satuan')
                                                                ->get(); 
                            }
                             */   
                            $Do_Detail[$rowCount]['Jumlah']   = $qty_done;// $details['qty_done'];              
                      
                            $uom_long_name =  $odoo->where('id','=',$details['product_uom_id'][0])                             
                                                   ->fields('description')
                                                   ->limit(1)                             
                                                   ->get('uom.uom');         
                                                   
                            $c_uom_long_name = count($uom_long_name);   
                            
                            if($c_uom_long_name<=0){                    
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Produk UOM ID '.$details['product_uom_id'][0].' Tidak ada di BISMySQl !'])->send(); 
                                exit;   
                            }                                                               
                            
                            //$satuan['0']['Satuan'] ;
                            $Do_Detail[$rowCount]['Satuan']      = $uom_long_name[0]['description']; // need long uom name
                            $Do_Detail[$rowCount]['Kadaluarsa']  = $batch[0]['expiry_date'];
                            $Do_Detail[$rowCount]['Terima']      = $qty_done;//$details['qty_done'];
                        
                            $program_promosi = sl_surat_pesanan_detail_model::where('No_SP','=',$no_sp[0]['kode_referensi'])
                                             ->where('Kode_Barang','=',$Do_Detail[$rowCount]['Kode_Barang']) 
                                             ->where('Satuan','=',$Do_Detail[$rowCount]['Satuan'])                                    
                                             ->limit(1) 
                                             ->get('ID_Program_Promosi');    
                            
                            $c_program_promosi = count($program_promosi);  
                            
                                                
                            if($c_program_promosi>0) 
                            {
                                $Do_Detail[$rowCount]['ID_Program_Promosi']= $program_promosi[0]['ID_Program_Promosi'];
                            }
                            else
                            {
                                $Do_Detail[$rowCount]['ID_Program_Promosi']='';
                            }        
                           
                            $rowCount++;   
                        } 

                         //then replace with new Record from Odoo
                        $is_saved=in_delivery_subdetail_model::insert($Do_Detail);
                        if($is_saved)
                        {   
                            $mapping['No_Delivery']  = $no_delivery[0]['origin'];   
                            $mapping['Flag_WMS']     = 'Picked';                       
                            $mapping['picking_name'] = $no_delivery[0]['name'];   
                            
                            in_delivery_flag_wms_model::where('no_delivery',$mapping['No_Delivery'])
                                                       ->update([
                                                       'Flag_WMS'=>$mapping['Flag_WMS'],
                                                       'picking_name'=>$mapping['picking_name']         
                                                      ]);
           
                            //$res['success']=1;                          
                            //$res['code']=200;                          
                            //$resVal=$res;
                            response()->json(['success'=>1,
                                              'code'=>200,
                                              'message'=>'Delivery Item [ '.$mapping['No_Delivery'].', Picking ID:'.$picking_id. ' ]  Berhasil di kirim ke BISMySQL !',
                                              'data'=>$Do_Detail])->send(); 
                            exit;
                        }  
                        else
                        {
                                                   
                            //$resVal=$res;
                            response()->json(['success'=>0,
                                              'code'=>400,
                                              'message'=>'Delivery Item '.$mapping['No_Delivery'].' Gagal dikirim !'])->send(); 
                            exit;
                        }                     
                }
                else 
                {
               //     $res['success']=0;    
               //     $res['code']   =400;                           
               //     $resVal=$res;
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'Data Stok Picking Tidak Valid !'])->send(); 
                    exit;
                }        
        }
        catch (Exception $e) 
        {
           // $res['success']=0;    
           // $res['code']   =400;                         
            //$resVal=$res;    
            response()->json(['success'=>0,
                              'code'=>400,
                              'message'=>'Data Stock Picking Tidak Valid !'])->send(); 
            exit;        
        }
        //return response()->json([$resVal]);        
    }


}
