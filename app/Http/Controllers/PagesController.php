<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function about()
    {
        return view('about');
    }

    public function services()
    {
        return view('services');
    }

    public function coverage()
    {
        return view('coverage');
    }

    public function specimenHandling()
    {
        return view('specimen-handling');
    }

    public function pricing()
    {
        return view('pricing');
    }

    public function contact()
    {
        return view('contact');
    }

    public function forms()
    {
        return view('forms');
    }

    public function hipaaNotice()
    {
        return view('hipaa-notice');
    }

    public function insurance()
    {
        return view('insurance', ['title' => 'Insurance Coverage']);
    }

    public function privacy()
    {
        return view('privacy', ['title' => 'Privacy Policy']);
    }

    public function terms()
    {
        return view('terms', ['title' => 'Terms and Conditions']);
    }
}