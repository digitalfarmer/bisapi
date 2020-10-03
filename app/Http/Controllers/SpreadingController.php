<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpreadingController extends Controller
{
    public function postPickingSpreading(Request $request)
    {
        response()->json([           
                         'result'=>$request->picking_id
                        ])->send();          
    }
}
