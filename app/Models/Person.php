<?php

namespace App\Models;

use App\Enums\DebtDirection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $table = 'people';

    protected $fillable = ['name', 'phone', 'note'];

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    /**
     * Adds receivable_left / payable_left columns: the unpaid remainder of open debts per direction.
     */
    public function scopeWithBalances(Builder $query): void
    {
        if (is_null($query->getQuery()->columns)) {
            $query->select('people.*');
        }

        foreach (DebtDirection::cases() as $direction) {
            $query->selectSub(
                Debt::query()
                    ->selectRaw('COALESCE(SUM(debts.amount - COALESCE((SELECT SUM(debt_payments.amount) FROM debt_payments WHERE debt_payments.debt_id = debts.id), 0)), 0)')
                    ->whereColumn('debts.person_id', 'people.id')
                    ->where('direction', $direction->value)
                    ->whereNull('settled_at'),
                $direction->value.'_left'
            );
        }
    }

    /** Positive: they owe me. Negative: I owe them. */
    public function getNetBalanceAttribute(): int
    {
        return (int) ($this->receivable_left ?? 0) - (int) ($this->payable_left ?? 0);
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/u', trim($this->name));

        return mb_substr($parts[0] ?? '', 0, 1).(isset($parts[1]) ? '‌'.mb_substr($parts[1], 0, 1) : '');
    }

    public function getAvatarColorAttribute(): string
    {
        $palette = ['#d9efe2', '#f7e3c8', '#dce4f5', '#f5d9de', '#e6dcf5', '#d6eef0'];

        return $palette[crc32($this->name) % count($palette)];
    }
}
