<?php

namespace App\Providers;

use App\Models\Tugas;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $notifikasi_deadline = null;

                if ($user->jabatan !== 'Admin') {
                    $tugas = Tugas::where('user_id', $user->id)->whereNull('file_tugas')->first();

                    if ($tugas) {
                        $tanggal_selesai = Carbon::parse($tugas->tanggal_selesai);
                        $hari_ini = Carbon::now();
                        $selisih_hari = $hari_ini->diffInDays($tanggal_selesai, false);

                        if ($selisih_hari >= 0 && $selisih_hari <= 2) { // H-3 berarti selisih hari 0, 1, 2
                            $notifikasi_deadline = [
                                'pesan' => 'Tugas \''. $tugas->tugas . '\' akan berakhir dalam ' . ($selisih_hari + 1) . ' hari.',
                                'tanggal' => $tanggal_selesai->format('d M Y'),
                                'link' => route('tugas')
                            ];
                        }
                    }
                }
                $view->with('notifikasi_deadline_global', $notifikasi_deadline);
            } else {
                $view->with('notifikasi_deadline_global', null);
            }
        });
    }
}
