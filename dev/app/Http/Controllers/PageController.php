<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactUsRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUsMail;

class PageController extends Controller
{
	/**
     * Show the application homescreen.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactMessage(ContactUsRequest $request) {

        /*try
        {*/
            Mail::to($request->user())->send(new ContactUsMail($request->all()));
            return redirect()->back()->with('success', 'Contact message has been sent');
        //}
        /*catch(Exception $e)
        {
            return redirect()->back()->with('error', 'Failed to send the message');
        }*/
    }
}
