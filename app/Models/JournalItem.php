<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $entry_id
 * @property int $account_id
 * @property string|null $type
 * @property float|null $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read JournalEntry $entry
 * @property-read AccountPlan $account
 *
 * @extends \Illuminate\Database\Eloquent\Model<self>
 */
class JournalItem extends Model
{
    protected $table = 'journal_items';

    protected $fillable = [
        'entry_id',
        'account_id',
        'type',
        'amount',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<JournalEntry, $this> */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'entry_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<AccountPlan, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountPlan::class, 'account_id');
    }
}
