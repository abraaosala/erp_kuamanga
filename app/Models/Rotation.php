<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                             $id
 * @property int                             $empresa_id
 * @property string                          $name
 * @property array<int, int>                 $pattern
 * @property string|null                     $start_date
 * @property string|null                     $end_date
 * @property string|null                     $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ScheduledShift> $scheduledShifts
 *
 * @method static \App\Models\Rotation create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class Rotation extends Model
{
    use SoftDeletes;

    protected $table = 'rotations';

    protected $fillable = [
        'empresa_id',
        'name',
        'pattern',
        'start_date',
        'end_date',
        'status',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'pattern'    => 'array',
        'start_date' => 'date',
        'end_date'   => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<ScheduledShift, $this> */
    public function scheduledShifts(): HasMany
    {
        return $this->hasMany(ScheduledShift::class, 'rotation_id');
    }
}
