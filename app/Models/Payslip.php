<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $empresa_id
 * @property int $payroll_run_id
 * @property int $employee_id
 * @property float $gross_salary
 * @property float $base_salary
 * @property float $overtime_amount
 * @property float $overtime_hours
 * @property float $absent_days
 * @property float $absent_deduction
 * @property float $net_salary
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa $empresa
 * @property-read PayrollRun $payrollRun
 * @property-read Employee $employee
 *
 * @method static \App\Models\Payslip create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class Payslip extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'payroll_run_id',
        'employee_id',
        'gross_salary',
        'base_salary',
        'overtime_amount',
        'overtime_hours',
        'absent_days',
        'absent_deduction',
        'net_salary',
        'status',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'gross_salary'     => 'decimal:2',
        'base_salary'      => 'decimal:2',
        'overtime_amount'  => 'decimal:2',
        'overtime_hours'   => 'decimal:2',
        'absent_days'      => 'decimal:2',
        'absent_deduction' => 'decimal:2',
        'net_salary'       => 'decimal:2',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<PayrollRun, $this> */
    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}