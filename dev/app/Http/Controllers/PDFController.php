<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function termsAndConditions()
    {
        $path = storage_path(config('marketmartial.documents.fsp.terms_and_conditions'));
        return response()->download($path);
    }

    public function privacyPolicy()
    {
        $path = storage_path(config('marketmartial.documents.fsp.privacy_policy'));
        return response()->download($path);
    }

    public function conflictsOfInterestPolicy()
    {
        $path = storage_path(config('marketmartial.documents.fsp.conflicts_of_interest_policy'));
        return response()->download($path);
    }

    public function tradingSpreads()
    {
        $path = storage_path(config('marketmartial.documents.fsp.trading_spreads'));
        return response()->download($path);
    }
}
