<?php

namespace Modules\Kategori\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Reservasi\Models\Reservasi;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function reservasis(): HasMany
    {
        return $this->hasMany(Reservasi::class);
    }
}
