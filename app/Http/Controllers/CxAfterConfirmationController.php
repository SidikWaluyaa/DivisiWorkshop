<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CxAfterConfirmationController extends Controller
{
    public function index()
    {
        return view('cx.after-confirmation.index');
    }
}
