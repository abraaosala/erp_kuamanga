<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    protected $casts = [
        'check_in_time'  => 'string',
        'check_out_time' => 'string',
        'break_minutes'  => 'integer',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
