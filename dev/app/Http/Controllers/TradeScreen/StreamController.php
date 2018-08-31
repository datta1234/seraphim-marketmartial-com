<?php

namespace App\Http\Controllers\TradeScreen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Broadcast\Stream;
use App\Http\Requests\StreamRequest;
class StreamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(StreamRequest $request)
    {
        return Stream::getChunks($request->input('checksum'),$request->input('missing_packets'));
    }
  
}
