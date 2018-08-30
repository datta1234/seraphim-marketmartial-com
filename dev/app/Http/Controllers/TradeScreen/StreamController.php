<?php

namespace App\Http\Controllers\TradeScreen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Broadcast\Stream;

class StreamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //0effba99d830561ae158e08c9085284b20e109ac2ec450f7fe73b60c10a23236
        return Stream::getChunks($request->input('checksum'),$request->input('missing_packets'));
        // return Stream::getChunks($request->input('checksum'),$request->input('missing_packets'));
    }
  
}
