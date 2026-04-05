<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('onboarding.index');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $user->onboarding_completed = true;
        $user->save();
        return redirect()->route('dashboard')->with('success', 'Welcome to Racksephnox!');
    }
}
