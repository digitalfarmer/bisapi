<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\stockopname\StockOpname;
use App\stockopname\StockOpnameMapping;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stock_header=StockOpname::paginate(20);

        return $stock_header;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $StockHeader = new StockOpname();
        $StockHeader->no_kertas_kerja = $request->no_kertas_kerja;
        $StockHeader->kode_principal = $request->kode_principal;
        $StockHeader->kode_divisi_produk = $request->kode_divisi_produk;
        $StockHeader->kode_gudang = $request->kode_gudang;
        $StockHeader->tanggal = $request->tanggal;
        $StockHeader->status = $request->status;
        $StockHeader->no_kertas_kerja = $request->no_kertas_kerja;
        $StockHeader->user_id = 'Odoo';
        $StockHeader->save();

        $stock_maping = new StockOpnameMapping();
        $stock_maping->no_kertas_kerja = $request->no_kertas_kerja;
        $stock_maping->id_adjustment = $request->id_adjustment;
        $stock_maping->save();
        
        return ('Succsess');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
