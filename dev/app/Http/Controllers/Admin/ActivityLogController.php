<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helpers\Logging\ActivityLogger;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.logging.index');
    }

    /**
     * Download log file filtered data
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $filter = [
            'user_id'           =>  $request->get('user'),
            'organisation_id'   =>  $request->get('organisation'),
            'activity_type'     =>  $request->get('activity_type'),
            'start_date'        =>  $request->get('start_date'),
            'end_date'        =>  $request->get('end_date'),
        ];
        // custom StringParser Closure
        $format = function($line) {
            return implode(' ', [
                "[".$line['date']->format("Y-m-d H:i:s")."]",
                $line['message'],
            ]);
        };
        $data = ActivityLogger::getLogData($filter['start_date'], $filter['end_date'], $filter, $format);
        
        // file construction
        $filename = implode('_', [
            $filter['activity_type'],
            $filter['organisation_id'],
            $filter['user_id'],
            $filter['start_date'],
            $filter['start_date'],
            $filter['end_date']
        ]).'.log';
        file_put_contents($filename, implode(PHP_EOL, $data));

        // response
        $headers = ['Content-Type' => 'text'];
        return response()->download($filename, $filename, $headers)->deleteFileAfterSend(true);
    }
}
