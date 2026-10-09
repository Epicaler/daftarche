<?php

namespace App\Models;

use App\Enums\DebtDirection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    protected $fillable = ['person_id', 'direction', 'amount', 'title', 'occurred_on', 'due_on', 'description', 'settled_at'];

    protected function casts(): array
    {
        return [
            'direction' => DebtDirection::class,
            'amount' => 'integer',
            'occurred_on' => 'date',
            'due_on' => 'date',
            'settled_at' => 'datetime',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function scopeOpen(Builder $query): void
    {
        $query->whereNull('settled_at');
    }

    public function scopeDirection(Builder $query, DebtDirection|string $direction): void
    {
        $query->where('direction', $direction instanceof DebtDirection ? $direction->value : $direction);
    }

    public function scopeWithPaid(Builder $query): void
    {
        $query->withSum('payments as paid', 'amount');
    }

    public function getPaidAmountAttribute(): int
    {
        if (array_key_exists('paid', $this->attributes)) {
            return (int) $this->attributes['paid'];
        }

        return (int) ($this->relationLoaded('payments') ? $this->payments->sum('amount') : $this->payments()->sum('amount'));
    }

    public function getRemainingAttribute(): int
    {
        return max(0, $this->amount - $this->paid_amount);
    }

    public function getProgressAttribute(): int
    {
        if ($this->settled_at) {
            return 100;
        }

        return $this->amount > 0 ? (int) min(100, round($this->paid_amount / $this->amount * 100)) : 0;
    }

    public function getStatusAttribute(): string
    {
        return match (true) {
            $this->settled_at !== null => 'settled',
            $this->due_on?->isBefore(today()) => 'overdue',
            $this->paid_amount > 0 => 'partial',
            default => 'open',
        };
    }

    public function isReceivable(): bool
    {
        return $this->direction === DebtDirection::Receivable;
    }

    /** Close the debt automatically once payments cover the whole amount, or reopen it. */
    public function syncSettlement(): void
    {
        $paid = (int) $this->payments()->sum('amount');
        $this->settled_at = $paid >= $this->amount ? ($this->settled_at ?? now()) : null;
        $this->save();
    }
}
