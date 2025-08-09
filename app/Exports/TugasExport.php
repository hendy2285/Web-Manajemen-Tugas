<?php

namespace App\Exports;

use App\Models\Tugas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TugasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tugas;

    public function __construct($tugas)
    {
        $this->tugas = $tugas;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->tugas;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Mahasiswa',
            'Tugas',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $row->user->nama,
            $row->tugas,
            $row->tanggal_mulai,
            $row->tanggal_selesai,
            $row->file_tugas ? 'Selesai' : 'Belum Selesai',
        ];
    }
}