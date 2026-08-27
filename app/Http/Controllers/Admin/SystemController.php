<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/**
 * SystemController - DISABLED FOR SAFETY
 * Feature "Pembersihan Sistem" has been permanently removed by administrator directive.
 */
class SystemController extends Controller
{
    public function index()
    {
        abort(404, 'Fitur Pembersihan Sistem telah dinonaktifkan demi keamanan data.');
    }

    public function reset()
    {
        abort(403, 'Aksi reset database global telah dinonaktifkan permanen.');
    }
}
