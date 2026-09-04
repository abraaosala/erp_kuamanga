<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $nome
 * @property string|null $nif
 * @property string|null $morada
 * @property string|null $codigo_postal
 * @property string|null $cidade
 * @property string|null $pais
 * @property string|null $regime_iva
 * @property string|null $cae
 * @property \Illuminate\Support\Carbon|null $data_constituicao
 * @property string|null $logo
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
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

    /** @var array<string, string> */
    protected $casts = [
        'data_constituicao' => 'date',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];
}
