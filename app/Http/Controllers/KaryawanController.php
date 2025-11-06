<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $karyawan = Karyawan::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $karyawan
        ]);
    }
}
