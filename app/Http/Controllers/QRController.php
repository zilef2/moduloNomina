<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QRController extends Controller
{
    public function index()
    {
        return view('qr-scanner');
    }
}
