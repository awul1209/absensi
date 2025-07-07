<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// PERBAIKAN 1: Kita tidak lagi membutuhkan RouteServiceProvider, jadi kita hapus
// use App\Providers\RouteServiceProvider; 
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    // PERBAIKAN 2: Langsung definisikan path redirect ke '/home'
    // Ini menghilangkan kebutuhan untuk memanggil RouteServiceProvider
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Buat user baru dengan role 'karyawan' secara default
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'karyawan', // Otomatis set role sebagai karyawan
        ]);

        // Otomatis buat profil karyawan yang terhubung
        if ($user) {
            Karyawan::create([
                'user_id' => $user->id,
                'nama_lengkap' => $user->name,
                'posisi' => 'Karyawan Baru', // Anda bisa set default posisi di sini
            ]);
        }
        
        return $user;
    }
}
