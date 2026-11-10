<?php

namespace Modules\Reservasi\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Kategori\Models\Kategori;

class Reservasi extends Model
{
    protected $table = 'reservasi';

    protected $fillable = [
        'kategori_id',
        'nama_pengguna',
        'nomor_plat',
        'file_stnk',
        'tanggal_reservasi',
        'durasi_jam',
        'status',
        'user_id',
        'denda_nominal',
        'denda_keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_reservasi' => 'date',
            'denda_nominal' => 'decimal:2',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
