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
 * @property \Illuminate\Support\Carbon $period_start
 * @property \Illuminate\Support\Carbon $period_end
 * @property string|null $description
 * @property string $status
 * @property float $total_gross
 * @property float $total_deductions
 * @property float $total_net
 * @property int $employee_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payslip> $payslips
 *
 * @method static \App\Models\PayrollRun create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class PayrollRun extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'period_start',
        'period_end',
        'description',
        'status',
        'total_gross',
        'total_deductions',
        'total_net',
        'employee_count',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'period_start'    => 'date',
        'period_end'      => 'date',
        'total_gross'     => 'decimal:2',
        'total_deductions'=> 'decimal:2',
        'total_net'       => 'decimal:2',
        'employee_count'  => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<Payslip, $this> */
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
}