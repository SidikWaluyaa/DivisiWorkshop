<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomLabelController extends Controller
{
    /**
     * Show the custom shipping label generator.
     */
    public function index()
    {
        return view('admin.custom-label.index');
    }
}
