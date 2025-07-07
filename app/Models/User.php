<?php

namespace App\Models;

// ... (use statements) ...
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // ... (use traits) ...

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // <-- PERBAIKAN: Pastikan 'role' ada di sini
    ];

    // ... (sisa kode model) ...
    
    /**
     * Mendefinisikan relasi "one-to-one" ke model Karyawan.
     */
    public function karyawan()
    {
        return $this->hasOne(Karyawan::class);
    }
}
