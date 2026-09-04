<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $empresa_id
 * @property string $name
 * @property string|null $check_in_time
 * @property string|null $check_out_time
 * @property int|null $break_minutes
 * @property string|null $days_of_week
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Employee> $employees
 *
 * @method static \App\Models\WorkSchedule create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class WorkSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'work_schedules';

    protected $fillable = [
        'empresa_id',
        'name',
        'check_in_time',
        'check_out_time',
        'break_minutes',
        'days_of_week',
        'status',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'check_in_time'  => 'string',
        'check_out_time' => 'string',
        'break_minutes'  => 'integer',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Employee, $this> */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_schedules', 'work_schedule_id', 'employee_id')
            ->withPivot(['is_default', 'start_date', 'end_date', 'empresa_id'])
            ->withTimestamps();
    }
}
