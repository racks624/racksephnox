<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalPagesController extends Controller
{
    /**
     * Show terms of service page
     */
    public function terms(): View
    {
        return view('legal.terms');
    }

    /**
     * Show privacy policy page
     */
    public function privacy(): View
    {
        return view('legal.privacy');
    }

    /**
     * Show cookie policy page
     */
    public function cookies(): View
    {
        return view('legal.cookies');
    }

    /**
     * Show compliance/regulatory page
     */
    public function compliance(): View
    {
        return view('legal.compliance');
    }
}
