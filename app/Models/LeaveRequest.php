<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Pedido de férias/licença (workflow pendente → aprovado/rejeitado/cancelado).
 *
 * @property int                             $id
 * @property int                             $empresa_id
 * @property int                             $employee_id
 * @property string                          $leave_type
 * @property \Illuminate\Support\Carbon      $start_date
 * @property \Illuminate\Support\Carbon      $end_date
 * @property int                             $days
 * @property string|null                     $reason
 * @property string                          $status
 * @property int|null                        $decided_by
 * @property \Illuminate\Support\Carbon|null $decided_at
 * @property string|null                     $decision_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read Employee $employee
 * @property-read Leave|null $leave
 *
 * @method static \App\Models\LeaveRequest create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class LeaveRequest extends Model
{
    use SoftDeletes;

    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_APROVADO = 'aprovado';

    public const STATUS_REJEITADO = 'rejeitado';

    public const STATUS_CANCELADO = 'cancelado';

    public const TYPE_FERIAS = 'ferias';

    public const TYPE_LICENCA_MATERNIDADE = 'licenca_maternidade';

    public const TYPE_LICENCA_PATERNIDADE = 'licenca_paternidade';

    public const TYPE_LICENCA_DOENCA = 'licenca_doenca';

    public const TYPE_LICENCA_REMUNERADA = 'licenca_remunerada';

    public const TYPE_LICENCA_NAO_REMUNERADA = 'licenca_nao_remunerada';

    public const TYPE_FALTA_JUSTIFICADA = 'falta_justificada';

    /** @var array<int, string> */
    public const LEAVE_TYPES = [
        self::TYPE_FERIAS,
        self::TYPE_LICENCA_MATERNIDADE,
        self::TYPE_LICENCA_PATERNIDADE,
        self::TYPE_LICENCA_DOENCA,
        self::TYPE_LICENCA_REMUNERADA,
        self::TYPE_LICENCA_NAO_REMUNERADA,
        self::TYPE_FALTA_JUSTIFICADA,
    ];

    /** @var array<string, string> */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'decided_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'empresa_id',
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'decided_by',
        'decided_at',
        'decision_notes',
    ];

    protected $table = 'leave_requests';

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

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<Leave, $this> */
    public function leave(): HasOne
    {
        return $this->hasOne(Leave::class);
    }
}
