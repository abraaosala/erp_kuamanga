<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $empresa_id
 * @property int $employee_id
 * @property string|null $tipo_contrato
 * @property \Illuminate\Support\Carbon|null $data_inicio
 * @property \Illuminate\Support\Carbon|null $data_fim
 * @property float|null $salario_base
 * @property int|null $carga_horaria
 * @property string|null $observacoes
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read Employee $employee
 *
 * @method static \App\Models\Contract create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'employee_id',
        'tipo_contrato',
        'data_inicio',
        'data_fim',
        'salario_base',
        'carga_horaria',
        'observacoes',
        'status',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'data_inicio' => 'date',
        'data_fim'    => 'date',
        'salario_base'=> 'decimal:2',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
