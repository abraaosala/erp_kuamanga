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
 * @property string|null $description
 * @property string|null $category
 * @property int|null $position_id
 * @property int|null $department_id
 * @property int $min_tenure_months
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read Position|null $position
 * @property-read Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Employee> $employees
 *
 * @method static \App\Models\Benefit create(array<array-key, mixed> $attributes = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Benefit withCount($relations)
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class Benefit extends Model
{
    use SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $table = 'benefits';

    protected $fillable = [
        'empresa_id',
        'name',
        'description',
        'category',
        'position_id',
        'department_id',
        'min_tenure_months',
        'status',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'min_tenure_months' => 'integer',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Position, $this> */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Employee, $this> */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_benefits', 'benefit_id', 'employee_id')
            ->withPivot(['status', 'started_at', 'notes'])
            ->withTimestamps();
    }
}