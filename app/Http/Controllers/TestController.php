<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class TestController extends Controller
{
    public function index(Request $request){
        $msg = "This is a simple message.";
        //return $request->all();
        //console.log($request);
        return response()->json(['foo' => $request->input('foo')], 200);
    }
}
