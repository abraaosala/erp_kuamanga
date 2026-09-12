<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                             $id
 * @property int|null                        $empresa_id
 * @property int                             $candidate_id
 * @property int|null                        $job_opening_id
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property string|null                     $interviewer
 * @property string                          $result
 * @property string|null                     $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Empresa|null $empresa
 * @property-read Candidate|null $candidate
 * @property-read JobOpening|null $jobOpening
 *
 * @method static \App\Models\Interview create(array<array-key, mixed> $attributes = [])
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class Interview extends Model
{
    use SoftDeletes;

    public const RESULT_PENDENTE = 'pendente';

    public const RESULT_APROVADO = 'aprovado';

    public const RESULT_REPROVADO = 'reprovado';

    public const RESULTS = [
        self::RESULT_PENDENTE,
        self::RESULT_APROVADO,
        self::RESULT_REPROVADO,
    ];

    protected $table = 'interviews';

    protected $fillable = [
        'empresa_id',
        'candidate_id',
        'job_opening_id',
        'scheduled_at',
        'interviewer',
        'result',
        'notes',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Candidate, $this> */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<JobOpening, $this> */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }
}
