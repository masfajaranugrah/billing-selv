<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SaldoAwal extends Model
{
    use HasFactory;

    protected $table = 'saldo_awals';

    // Matikan auto increment
    public $incrementing = false;

    // Tipe primary key adalah string (UUID)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'bulan',
        'tahun',
        'omset_dedicated',
        'omset_homenet_kotor',
        'omset_homenet_bersih',
    ];

    protected $casts = [
        'omset_dedicated' => 'decimal:2',
        'omset_homenet_kotor' => 'decimal:2',
        'omset_homenet_bersih' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate UUID sebelum create
        static::creating(function ($saldoAwal) {
            if (! $saldoAwal->id) {
                $saldoAwal->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get saldo awal for specific month and year
     */
    public static function getByPeriod($bulan, $tahun)
    {
        return self::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();
    }
}
