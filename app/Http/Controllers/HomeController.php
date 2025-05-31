<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Import Auth Facade

class HomeController extends Controller
{
    /**
     * Menampilkan landing page jika belum login,
     * atau redirect ke dashboard jika sudah login.
     */
    public function index()
    {
        // Cek apakah pengguna sudah login
        if (Auth::check()) {
            // Jika ya, redirect ke halaman dashboard
            return redirect()->route('dashboard');
        }

        // Jika belum, tampilkan view landing page 'welcome'
        return view('welcome');
    }
}