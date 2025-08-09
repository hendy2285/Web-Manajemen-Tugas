<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Psy\Command\WhereamiCommand;
use Illuminate\Support\Facades\Auth;

use App\Exports\TugasExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->jabatan == 'Admin') {
            $query = Tugas::with('user');

            // Search
            if ($request->has('search') && $request->search != '') {
                $query->where(function ($q) use ($request) {
                    $q->where('tugas', 'like', '%' . $request->search . '%')
                        ->orWhereHas('user', function ($q) use ($request) {
                            $q->where('nama', 'like', '%' . $request->search . '%');
                        });
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'selesai') {
                    $query->whereNotNull('file_tugas');
                } elseif ($request->status == 'belum') {
                    $query->whereNull('file_tugas');
                }
            }

            // Sorting
            $sort = $request->get('sort', 'tanggal_selesai'); // default sort
            $direction = $request->get('direction', 'desc'); // default direction

            if ($sort == 'nama') {
                $query->join('users', 'tugas.user_id', '=', 'users.id')
                      ->orderBy('users.nama', $direction)
                      ->select('tugas.*');
            } else {
                $query->orderBy($sort, $direction);
            }

            $data = array(
                'title' => 'Data Tugas',
                'menuAdminTugas' => 'active',
                'tugas' => $query->paginate(10)->withQueryString(),
            );
            return view('admin/tugas/index', $data);
        } else {
            $data = array(
                'title' => 'Data Tugas',
                'menuMahasiswaTugas' => 'active',
                'tugas' => Tugas::with('user')->where('user_id', $user->id)->first(),
            );
            return view('mahasiswa/tugas/index', $data);
        }
    }

    private function getFilteredTugas(Request $request)
    {
        $query = Tugas::with('user');

        // Search logic from index
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('tugas', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('nama', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Filter by status logic from index
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'selesai') {
                $query->whereNotNull('file_tugas');
            } elseif ($request->status == 'belum') {
                $query->whereNull('file_tugas');
            }
        }

        // Sorting logic from index
        $sort = $request->get('sort', 'tanggal_selesai');
        $direction = $request->get('direction', 'desc');

        if ($sort == 'nama') {
            $query->join('users', 'tugas.user_id', '=', 'users.id')
                  ->orderBy('users.nama', $direction)
                  ->select('tugas.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        return $query->get();
    }

    public function exportExcel(Request $request)
    {
        $tugas = $this->getFilteredTugas($request);
        return Excel::download(new TugasExport($tugas), 'data-tugas.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tugas = $this->getFilteredTugas($request);
        $pdf = Pdf::loadView('admin.tugas.pdf', ['tugas' => $tugas]);
        return $pdf->download('data-tugas.pdf');
    }

    public function create()
    {
        $data = array(
            'title' => 'Tambah Data Tugas',
            'menuAdminTugas' => 'active',
            'user' => User::where('jabatan', 'Mahasiswa')->where('is_tugas', false)->get(),
        );
        return view('admin/tugas/create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'tugas' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ], [
            'user_id.required' => 'Nama Tidak Boleh Kosong',
            'tugas.required' => 'Tugas Tidak Boleh Kosong',
            'tanggal_mulai.required' => 'Tanggal Mulai Tidak Boleh Kosong',
            'tanggal_selesai.required' => 'Tanggal Selesai Tidak Boleh Kosong',
        ]);

        $user = User::findOrFail($request->user_id);
        $tugas = new Tugas;
        $tugas->user_id = $request->user_id;
        $tugas->tugas = $request->tugas;
        $tugas->tanggal_mulai = $request->tanggal_mulai;
        $tugas->tanggal_selesai = $request->tanggal_selesai;
        $tugas->save();

        $user->is_tugas = true;
        $user->save();

        return redirect()->route('tugas')->with('success', 'Data Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $data = array(
            'title' => 'Edit Data Tugas',
            'menuAdminTugas' => 'active',
            'tugas' => Tugas::with('user')->findOrFail($id),

        );
        return view('admin/tugas/update', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tugas' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ], [
            'tugas.required' => 'Tugas Tidak Boleh Kosong',
            'tanggal_mulai.required' => 'Tanggal Mulai Tidak Boleh Kosong',
            'tanggal_selesai.required' => 'Tanggal Selesai Tidak Boleh Kosong',
        ]);
        $tugas = Tugas::findOrFail($id);
        $tugas->tugas = $request->tugas;
        $tugas->tanggal_mulai = $request->tanggal_mulai;
        $tugas->tanggal_selesai = $request->tanggal_selesai;
        $tugas->save();

        return redirect()->route('tugas')->with('success', 'Data Berhasil DiEdit');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->delete();
        $user = User::where('id', $tugas->user_id)->first();
        $user->is_tugas = false;
        $user->save();

        return redirect()->route('tugas')->with('success', 'Data Berhasil DiHapus');
    }

    public function upload(Request $request, $id)
    {
                        $request->validate([
            'file_tugas' => 'required|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:2048'
        ]);

        $tugas = Tugas::findOrFail($id);
        $file = $request->file('file_tugas');
        if (!$file) {
            \Log::error('No file uploaded.');
            return redirect()->back()->with('error', 'Gagal mengunggah tugas: Tidak ada file yang diunggah.');
        }

        if (!$file->isValid()) {
            $errorMessage = 'Unknown error.';
            if ($file->getError()) {
                $errorMessage = 'PHP Upload Error Code: ' . $file->getError();
                // You can map error codes to messages for more detail
                // For example: UPLOAD_ERR_INI_SIZE (1), UPLOAD_ERR_FORM_SIZE (2), UPLOAD_ERR_PARTIAL (3), UPLOAD_ERR_NO_FILE (4), etc.
            }
            \Log::error('Uploaded file is not valid. Error: ' . $errorMessage);
            return redirect()->back()->with('error', 'Gagal mengunggah tugas: File tidak valid. ' . $errorMessage);
        }

        if (!$file->getRealPath()) {
            \Log::error('Uploaded file temporary path is missing.');
            return redirect()->back()->with('error', 'Gagal mengunggah tugas: Jalur sementara file tidak ditemukan.');
        }

        $filename = time() . '-' . $file->getClientOriginalName();
        try {
            $file->storeAs('tugas', $filename, 'public');
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunggah tugas: ' . $e->getMessage());
        }

        $tugas->file_tugas = $filename;
        $tugas->save();

        return redirect()->back()->with('success', 'Tugas berhasil diupload');
    }
}
