<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPlan extends Model
{
    use SoftDeletes;

    protected $table = 'account_plans';

    protected $fillable = [
        'empresa_id',
        'parent_id',
        'code',
        'name',
        'type',
        'is_analytic',
        'status',
    ];

    protected $casts = [
        'is_analytic' => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountPlan::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AccountPlan::class, 'parent_id');
    }
}
