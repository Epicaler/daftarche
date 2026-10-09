<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'type', 'color'];

    protected function casts(): array
    {
        return ['type' => TransactionType::class];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeOfType(Builder $query, TransactionType|string $type): void
    {
        $query->where('type', $type instanceof TransactionType ? $type->value : $type);
    }
}
