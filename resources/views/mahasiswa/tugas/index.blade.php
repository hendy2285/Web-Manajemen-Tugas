@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-tasks mr-2"></i>
        {{ $title}}
    </h1>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between"> </div>
        <div class="card-body">

            @if (auth()->user()->is_tugas == true)
                <div class="row">
                    <div class="col-6">
                        Nama
                    </div>
                    <div class="col-6">
                        : {{ $tugas->user->nama }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        Email
                    </div>
                    <div class="col-6">
                        :
                        <span class="badge badge-primary">
                            {{ $tugas->user->email }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        Tugas
                    </div>
                    <div class="col-6">
                        : {{ $tugas->tugas }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        Tanggal Mulai
                    </div>
                    <div class="col-6">
                        : <span class="badge badge-info">
                            {{ $tugas->tanggal_mulai }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        Tanggal Selesai
                    </div>
                    <div class="col-6">
                        : <span class="badge badge-info">
                            {{ $tugas->tanggal_selesai }}
                        </span>
                    </div>
                </div>

                <hr>

                <form action="{{ route('tugasUpload', $tugas->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Upload Tugas</label>
                        <input type="file" name="file_tugas" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            @else
                <div class="row">
                    <div class="col-6">
                        Status
                    </div>
                    <div class="col-6">
                        :
                        <span class="badge badge-danger">
                            Belum Ditugaskan
                        </span>
                    </div>
                </div>
            @endif



        </div>
    </div>
@endsection