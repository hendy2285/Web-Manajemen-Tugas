@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-edit mr-2"></i>
        {{ $title}}
    </h1>

    <div class="card">
        <div class="card-header bg-warning">
            <a href="{{ route('user') }}" class="btn btn-sm btn-success">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali</a>
        </div>
        <div class="card-body">

            <form action="{{ route('userUpdate',$user->id) }}" method="post">
                @csrf
                {{-- Input Nama dan Email --}}
                <div class="row  mb-2">
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Nama :</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                            value="{{ $user->nama }}">
                        @error('nama')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Email :</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ $user->email }}">
                        @error('email')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                </div>
                {{-- Pilih Jabatan --}}
                <div class="row mb-2">
                    <div class="col-xl-12">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Jabatan :</label>
                        <select name="jabatan" class="form-control @error('jabatan') is-invalid @enderror">
                            <option disabled>--Pilih Jabatan--</option>
                            <option value="Admin" {{ $user->jabatan == 'Admin'? 'selected' : '' }}>Admin</option>
                            <option value="Mahasiswa" {{ $user->jabatan == 'Mahasiswa'? 'selected' : '' }}>Mahasiswa</option>
                        </select>
                        @error('jabatan')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                </div>
                {{-- Password dan Konfirmasi --}}
                <div class="row  mb-2">
                    <div class="col-xl-6 mb-2">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Password :</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                    <div class="col-xl-6">
                        <label class="form-label">
                            <span class="text-danger">*</span>
                            Konfirmasi Password :</label>
                        <input type="password" name="password_confirmation"
                            class="form-control @error('password') is-invalid @enderror">
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection