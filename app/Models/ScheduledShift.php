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
 * @property int|null $work_schedule_id
 * @property int|null $rotation_id
 * @property \Illuminate\Support\Carbon $date
 * @property string $classification
 * @property string $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read Employee $employee
 * @property-read WorkSchedule|null $workSchedule
 * @property-read Rotation|null $rotation
 *
 * @method static \App\Models\ScheduledShift create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class ScheduledShift extends Model
{
    use SoftDeletes;

    protected $table = 'scheduled_shifts';

    protected $fillable = [
        'empresa_id',
        'employee_id',
        'work_schedule_id',
        'rotation_id',
        'date',
        'classification',
        'source',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'date'         => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
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

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<WorkSchedule, $this> */
    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Rotation, $this> */
    public function rotation(): BelongsTo
    {
        return $this->belongsTo(Rotation::class);
    }
}