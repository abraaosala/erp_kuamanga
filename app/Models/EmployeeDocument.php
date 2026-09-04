<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $employee_id
 * @property int|null $empresa_id
 * @property string $document_type
 * @property string|null $document_number
 * @property string $file_path
 * @property string $file_name
 * @property int|null $file_size
 * @property string|null $mime_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Employee $employee
 * @property-read Empresa|null $empresa
 *
 * @method static \App\Models\EmployeeDocument create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class EmployeeDocument extends Model
{
    protected $table = 'employee_documents';

    protected $fillable = [
        'employee_id',
        'empresa_id',
        'document_type',
        'document_number',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'file_size'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
