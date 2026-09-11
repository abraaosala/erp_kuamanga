<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Registo efectivo de férias/licença gozada (consumo de saldo).
 *
 * @property int $id
 * @property int $empresa_id
 * @property int $employee_id
 * @property int|null $leave_request_id
 * @property string $leave_type
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int $days
 * @property string $status
 * @property string|null $observations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read Employee $employee
 * @property-read LeaveRequest|null $leaveRequest
 *
 * @method static \App\Models\Leave create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class Leave extends Model
{
    use SoftDeletes;

    public const STATUS_GOZADA = 'gozada';

    public const STATUS_PROGRAMADA = 'programada';

    public const STATUS_CANCELADA = 'cancelada';

    public const TYPE_FERIAS = 'ferias';

    /** @var array<string, string> */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'empresa_id',
        'employee_id',
        'leave_request_id',
        'leave_type',
        'start_date',
        'end_date',
        'days',
        'status',
        'observations',
    ];

    protected $table = 'leaves';

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

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<LeaveRequest, $this> */
    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }
}