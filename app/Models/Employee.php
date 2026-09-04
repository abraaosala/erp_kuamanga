<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $empresa_id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property int|null $department_id
 * @property int|null $position_id
 * @property \Illuminate\Support\Carbon|null $hire_date
 * @property string|null $status
 * @property string|null $bi
 * @property string|null $inss
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read Department|null $department
 * @property-read Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Contract> $contracts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, WorkSchedule> $schedules
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EmployeeDocument> $documents
 *
 * @method static \App\Models\Employee create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class Employee extends Model
{
    use SoftDeletes;

    protected $table = 'employees';

    protected $fillable = [
        'empresa_id',
        'name',
        'email',
        'phone',
        'department_id',
        'position_id',
        'hire_date',
        'status',
        'bi',
        'inss',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'hire_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Position, $this> */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<Contract, $this> */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<WorkSchedule, $this> */
    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(WorkSchedule::class, 'employee_schedules', 'employee_id', 'work_schedule_id')
            ->withPivot(['is_default', 'start_date', 'end_date', 'empresa_id'])
            ->withTimestamps();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<EmployeeDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }
}
