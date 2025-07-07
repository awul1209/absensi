<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard based on user role.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $role = Auth::user()->role;

        if ($role == 'admin') {
            // Jika role adalah admin, arahkan ke route 'admin.dashboard'
            return redirect()->route('admin.dashboard');
        } elseif ($role == 'karyawan') {
            // Jika role adalah karyawan, arahkan ke route 'karyawan.dashboard'
            return redirect()->route('karyawan.dashboard');
        } else {
            // Fallback jika user tidak punya role (seharusnya tidak terjadi)
            Auth::logout();
            return redirect('/login')->with('error', 'Peran Anda tidak dikenali.');
        }
    }
}
