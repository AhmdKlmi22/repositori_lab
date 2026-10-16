<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Menampilkan view dashboard utama dengan data user yang sedang login
        return view('dashboard.index');
    }
}