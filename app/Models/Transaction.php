<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['type', 'amount', 'category_id', 'title', 'occurred_on', 'description'];

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'amount' => 'integer',
            'occurred_on' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeIncome(Builder $query): void
    {
        $query->where('type', TransactionType::Income->value);
    }

    public function scopeExpense(Builder $query): void
    {
        $query->where('type', TransactionType::Expense->value);
    }

    public function scopeBetween(Builder $query, $from, $to): void
    {
        $query->whereBetween('occurred_on', [$from->toDateString(), $to->toDateString()]);
    }

    public function isIncome(): bool
    {
        return $this->type === TransactionType::Income;
    }
}
