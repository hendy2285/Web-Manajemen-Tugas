@extends('layouts/app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-tasks mr-2"></i>
        {{ $title }}
    </h1>

    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="mb-2">
                    <a href="{{ route('tugasCreate') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Data
                    </a>
                </div>
                <div class="mb-2">
                    <form action="{{ route('tugas') }}" method="GET" class="form-inline">
                        <div class="form-group mr-2">
                            <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Cari Nama/Tugas...">
                        </div>
                        <div class="form-group mr-2">
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Selesai</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('tugas') }}" class="btn btn-sm btn-secondary ml-1" title="Reset Filter">
                            <i class="fas fa-sync"></i>
                        </a>

                        <div class="btn-group ml-2">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-file-export mr-1"></i> Export
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('tugasExportExcel', request()->query()) }}">
                                    <i class="fas fa-file-excel mr-2"></i> Excel
                                </a>
                                <a class="dropdown-item" href="{{ route('tugasExportPdf', request()->query()) }}">
                                    <i class="fas fa-file-pdf mr-2"></i> PDF
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    @php
                        function sortable($label, $column) {
                            $direction = (request('sort') == $column && request('direction') == 'asc') ? 'desc' : 'asc';
                            $icon = '';
                            if (request('sort') == $column) {
                                $icon = request('direction') == 'asc' ? '<i class="fas fa-sort-up ml-1"></i>' : '<i class="fas fa-sort-down ml-1"></i>';
                            }
                            return '<a href="' . route('tugas', array_merge(request()->query(), ['sort' => $column, 'direction' => $direction])) . '">' . $label . ' ' . $icon . '</a>';
                        }
                    @endphp
                    <thead class="bg-primary text-white">
                        <tr class="text-center">
                            <th>No</th>
                            <th>{!! sortable('Nama', 'nama') !!}</th>
                            <th>{!! sortable('Tugas', 'tugas') !!}</th>
                            <th>{!! sortable('Tanggal Mulai', 'tanggal_mulai') !!}</th>
                            <th>{!! sortable('Tanggal Selesai', 'tanggal_selesai') !!}</th>
                            <th>File Tugas</th>
                            <th><i class="fas fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tugas as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + ($tugas->currentPage() - 1) * $tugas->perPage() }}</td>
                                <td>{{ $item->user->nama }}</td>
                                <td>{{ $item->tugas }}</td>
                                <td class="text-center">
                                    <span class="badge badge-info">
                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">
                                        {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($item->file_tugas)
                                        <a href="{{ asset('storage/tugas/' . $item->file_tugas) }}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @else
                                        <span class="badge badge-danger">Belum Upload</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" data-toggle="modal"
                                        data-target="#modalTugasShow{{ $item->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('tugasEdit', $item->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" data-toggle="modal"
                                        data-target="#modalTugasDestroy{{ $item->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @include('admin/tugas/modal')
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    {{ $tugas->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
