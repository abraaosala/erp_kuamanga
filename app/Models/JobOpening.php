<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                             $id
 * @property int|null                        $empresa_id
 * @property string                          $title
 * @property int|null                        $department_id
 * @property int|null                        $position_id
 * @property string|null                     $description
 * @property string|null                     $requirements
 * @property int                             $openings_count
 * @property float|null                      $salary_range_min
 * @property float|null                      $salary_range_max
 * @property string|null                     $location
 * @property string                          $status
 * @property string|null                     $closes_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa|null $empresa
 * @property-read Department|null $department
 * @property-read Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Candidate> $candidates
 *
 * @method static \App\Models\JobOpening create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class JobOpening extends Model
{
    use SoftDeletes;

    public const STATUS_ABERTA = 'aberta';

    public const STATUS_PAUSADA = 'pausada';

    public const STATUS_ENCERRADA = 'encerrada';

    public const STATUSES = [
        self::STATUS_ABERTA,
        self::STATUS_PAUSADA,
        self::STATUS_ENCERRADA,
    ];

    protected $table = 'job_openings';

    protected $fillable = [
        'empresa_id',
        'title',
        'department_id',
        'position_id',
        'description',
        'requirements',
        'openings_count',
        'salary_range_min',
        'salary_range_max',
        'location',
        'status',
        'closes_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'openings_count'  => 'integer',
        'salary_range_min' => 'float',
        'salary_range_max' => 'float',
        'closes_at'        => 'date',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
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

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<Candidate, $this> */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'job_opening_id');
    }
}
