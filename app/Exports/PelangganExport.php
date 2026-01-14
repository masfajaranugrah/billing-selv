<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelangganExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Pelanggan::query()
            ->with(['paket:id,nama_paket,kecepatan,harga'])
            ->select(
                'id',
                'nama_lengkap',
                'nomer_id',
                'no_whatsapp',
                'alamat_jalan',
                'rt',
                'rw',
                'kecamatan',
                'kabupaten',
                'paket_id'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'ID Pelanggan',
            'No HP',
            'Alamat',
            'Kecepatan',
            'Biaya Langganan',
        ];
    }

    public function map($row): array
    {
        // Format alamat lengkap
        $alamat = $row->alamat_jalan ?? '-';
        if ($row->rt || $row->rw) {
            $alamat .= ', RT ' . ($row->rt ?? '-') . '/RW ' . ($row->rw ?? '-');
        }
        if ($row->kecamatan) {
            $alamat .= ', ' . $row->kecamatan;
        }
        if ($row->kabupaten) {
            $alamat .= ', ' . $row->kabupaten;
        }

        return [
            $row->nama_lengkap ?? '-',
            $row->nomer_id ?? '-',
            $row->no_whatsapp ?? '-',
            $alamat,
            $row->paket->kecepatan ?? '-',
            $row->paket ? 'Rp ' . number_format($row->paket->harga, 0, ',', '.') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style for header row
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '18181b']
                ],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
