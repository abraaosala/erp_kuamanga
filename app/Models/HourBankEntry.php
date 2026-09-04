<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HourBankEntry extends Model
{
    use SoftDeletes;

    protected $table = 'hour_bank_entries';

    protected $fillable = [
        'empresa_id',
        'employee_id',
        'date',
        'hours',
        'type',
        'observations',
    ];

    protected $casts = [
        'date'       => 'date',
        'hours'      => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
