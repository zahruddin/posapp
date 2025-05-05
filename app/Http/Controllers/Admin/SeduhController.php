<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;


class SeduhController extends Controller
{
    //
    public function index(){
        $users = User::with('outlet')->paginate(10); // Ambil 5 data per halaman
        $outlets = Outlet::all(); 
        return view('admin.seduh', compact('users','outlets'));
    }

}
