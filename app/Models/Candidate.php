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
 * @property int|null                        $job_opening_id
 * @property string                          $name
 * @property string                          $email
 * @property string|null                     $phone
 * @property string                          $source
 * @property string                          $status
 * @property int|null                        $decided_by
 * @property \Illuminate\Support\Carbon|null $decided_at
 * @property int|null                        $employee_id
 * @property string|null                     $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa|null $empresa
 * @property-read JobOpening|null $jobOpening
 * @property-read Employee|null $employee
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Interview> $interviews
 *
 * @method static \App\Models\Candidate create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class Candidate extends Model
{
    use SoftDeletes;

    public const STATUS_NOVO = 'novo';

    public const STATUS_TRIAGEM = 'triagem';

    public const STATUS_ENTREVISTA = 'entrevista';

    public const STATUS_APROVADO = 'aprovado';

    public const STATUS_REJEITADO = 'rejeitado';

    public const STATUS_CONTRATADO = 'contratado';

    public const STATUSES = [
        self::STATUS_NOVO,
        self::STATUS_TRIAGEM,
        self::STATUS_ENTREVISTA,
        self::STATUS_APROVADO,
        self::STATUS_CONTRATADO,
        self::STATUS_REJEITADO,
    ];

    public const SOURCE_SITE = 'site';

    public const SOURCE_REFERENCIA = 'referencia';

    public const SOURCE_SITE_EMPREGO = 'site_emprego';

    public const SOURCE_REDE_SOCIAL = 'rede_social';

    public const SOURCE_OUTRO = 'outro';

    /** Estados terminais da pipeline (não aceitam mais acções de decisão). */
    public const TERMINAL_STATUSES = [
        self::STATUS_REJEITADO,
        self::STATUS_CONTRATADO,
    ];

    public const SOURCES = [
        self::SOURCE_SITE,
        self::SOURCE_REFERENCIA,
        self::SOURCE_SITE_EMPREGO,
        self::SOURCE_REDE_SOCIAL,
        self::SOURCE_OUTRO,
    ];

    protected $table = 'candidates';

    protected $fillable = [
        'empresa_id',
        'job_opening_id',
        'name',
        'email',
        'phone',
        'source',
        'status',
        'decided_by',
        'decided_at',
        'employee_id',
        'notes',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'decided_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<JobOpening, $this> */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<Interview, $this> */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'candidate_id');
    }
}
