<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PelangganExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Pelanggan::query()
            ->with(['loginStatus:id,pelanggan_id,is_active'])
            ->select(
                'id',
                'nama_lengkap',
                'nomer_id',
                'no_whatsapp',
                'created_at'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Nomer ID',
            'Telp',
            'Status',
            'Tanggal Daftar',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nama_lengkap ?? '-',
            $row->nomer_id ?? '-',
            $row->no_whatsapp ?? '-',

            // Status login dari tabel statuses
            ($row->loginStatus && $row->loginStatus->is_active === true)
                ? 'Aktif'
                : 'Tidak Aktif',

            optional($row->created_at)->format('d-m-Y') ?? '-',
        ];
    }
}
