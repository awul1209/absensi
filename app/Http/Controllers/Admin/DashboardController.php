<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Nanti Anda bisa menambahkan data di sini, contoh:
        // $jumlahKaryawan = \App\Models\Karyawan::count();
        // return view('admin.dashboard', compact('jumlahKaryawan'));

        return view('admin.dashboard');
    }
}