<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $empresa_id
 * @property int|null $parent_id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property bool $is_analytic
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read AccountPlan|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AccountPlan> $children
 *
 * @method static \App\Models\AccountPlan create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
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

    /** @var array<string, string> */
    protected $casts = [
        'is_analytic' => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<AccountPlan, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountPlan::class, 'parent_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<AccountPlan, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(AccountPlan::class, 'parent_id');
    }
}
