<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use SoftDeletes;

    protected $table = 'empresas';

    protected $fillable = [
        'nome',
        'nif',
        'morada',
        'codigo_postal',
        'cidade',
        'pais',
        'regime_iva',
        'cae',
        'data_constituicao',
        'logo',
        'status',
    ];

    protected $casts = [
        'data_constituicao' => 'date',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];
}
