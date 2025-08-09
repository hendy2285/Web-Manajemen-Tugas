<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $data = array(
            "title" => "Dashboard",
            "menuDashboard" => "active",
            "jumlahUser" => User::count(),
            "jumlahAdmin" => User::where('jabatan', 'Admin')->count(),
            "jumlahMahasiswa" => User::where('jabatan', 'Mahasiswa')->count(),
            "jumlahDitugaskan" => User::where('jabatan', 'Mahasiswa')->where('is_tugas', True)->count(),
            "jumlahBelumDitugaskan" => User::where('jabatan', 'Mahasiswa')->where('is_tugas', false)->count(),
        );
        return view('dashboard', $data);
    }
}
