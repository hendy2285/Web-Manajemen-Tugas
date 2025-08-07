<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Data User',
            'menuAdminUser' => 'active',
            'user' => User::orderBy('jabatan', 'asc')->get(),
        );
        return view('admin/user/index', $data);
    }
    public function create()
    {
        $data = array(
            'title' => 'Tambah Data User',
            'menuAdminUser' => 'active',
        );
        return view('admin/user/create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|unique:users,email',
            'jabatan' => 'required',
            'password' => 'required|confirmed|min:8',
        ], [
            'nama.required' => 'Nama Tidak Boleh Kosong',
            'email.required' => 'Email Tidak Boleh Kosong',
            'email.unique' => 'Email Sudah Ada',
            'jabatan.required' => 'Jabatan Harus Dipilih',
            'password.required' => 'Password Tidak Boleh Kosong',
            'password.confirmed' => 'Password Konfirmasi Tidak Sama',
            'password.min' => 'Password Minimal 8 Karakter',
        ]);

        $user = new User;
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->jabatan = $request->jabatan;
        $user->password = Hash::make($request->password);
        $user->is_tugas = false;
        $user->save();

        return redirect()->route('user')->with('success', 'Data Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $data = array(
            'title' => 'Edit Data User',
            'menuAdminUser' => 'active',
            'user' => User::findOrFail($id),
        );
        return view('admin/user/edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|unique:users,email,' . $id,
            'jabatan' => 'required',
            'password' => 'nullable|confirmed|min:8',
        ], [
            'nama.required' => 'Nama Tidak Boleh Kosong',
            'email.required' => 'Email Tidak Boleh Kosong',
            'email.unique' => 'Email Sudah Ada',
            'jabatan.required' => 'Jabatan Harus Dipilih',
            'password.confirmed' => 'Password Konfirmasi Tidak Sama',
            'password.min' => 'Password Minimal 8 Karakter',
        ]);

        $user = User::with('tugas')->findOrFail($id);
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->jabatan = $request->jabatan;
        if ($request->jabatan == "Admin") {
            $user->is_tugas = false;
            $user->tugas()->delete();
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('user')->with('success', 'Data Berhasil DiUpdate');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user')->with('success', 'Data Berhasil DiHapus');
    }

}
