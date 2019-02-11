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

    public function feeStructures()
    {
        return view('pages.fee_structures');
    }

    public function fspDisclosures()
    {
        return view('pages.fsp_disclosures');
    }

    public function contactMessage(ContactUsRequest $request) {

        Mail::to(config("mail.admin_email"))->send(new ContactUsMail($request->all()));

        if( count(Mail::failures()) > 0 ) {
            \Log::error(["Contact Us Mail Failures: " => Mail::failures()]);
           return redirect()->back()->with('error', 'Failed to send the message');
        } else {
            return redirect()->action('PageController@index')->with('success', 'Contact message has been sent');
        }
    }
}
