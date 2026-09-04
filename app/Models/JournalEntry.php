<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $empresa_id
 * @property \Illuminate\Support\Carbon $date
 * @property string|null $description
 * @property string|null $reference
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Empresa $empresa
 * @property-read \Illuminate\Database\Eloquent\Collection<int, JournalItem> $items
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class JournalEntry extends Model
{
    protected $table = 'journal_entries';

    protected $fillable = [
        'empresa_id',
        'date',
        'description',
        'reference',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'date'       => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Empresa, $this> */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<JournalItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(JournalItem::class, 'entry_id');
    }
}
