<?php

namespace App\Livewire\Transactions;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Support\Jalali;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('دخل و خرج')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $type = '';

    #[Url]
    public string $category = '';

    /** Jalali month as "1405-07", or empty for all time. */
    #[Url]
    public string $month = '';

    public function updated($property): void
    {
        if ($property === 'type') {
            $this->category = '';
        }

        $this->resetPage();
    }

    #[On('transaction-saved')]
    public function refreshList(): void
    {
        //
    }

    public function delete(int $id): void
    {
        Transaction::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'تراکنش حذف شد');
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'type', 'category', 'month');
        $this->resetPage();
    }

    #[Computed]
    public function months(): array
    {
        [$jy, $jm] = Jalali::yearMonth();

        return collect(range(0, 23))
            ->mapWithKeys(function ($back) use ($jy, $jm) {
                [$y, $m] = Jalali::shiftMonth($jy, $jm, -$back);

                return [sprintf('%d-%02d', $y, $m) => Jalali::monthLabel($y, $m)];
            })
            ->all();
    }

    #[Computed]
    public function categories()
    {
        return Category::query()->when($this->type, fn ($q) => $q->ofType($this->type))->orderBy('type')->orderBy('name')->get();
    }

    protected function filtered(): Builder
    {
        return Transaction::query()
            ->when($this->search, fn ($q) => $q->where(fn ($q) => $q
                ->where('title', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%")))
            ->when(TransactionType::tryFrom($this->type), fn ($q, $type) => $q->where('type', $type->value))
            ->when($this->category === 'none', fn ($q) => $q->whereNull('category_id'))
            ->when(is_numeric($this->category), fn ($q) => $q->where('category_id', (int) $this->category))
            ->when(preg_match('/^(\d{4})-(\d{2})$/', $this->month, $m) ? $m : null,
                fn ($q, $m) => $q->between(...Jalali::monthRange((int) $m[1], (int) $m[2])));
    }

    public function render()
    {
        $totals = $this->filtered()->selectRaw('type, SUM(amount) as total, COUNT(*) as count')->groupBy('type')->get()->keyBy(fn ($r) => $r->type->value);

        return view('livewire.transactions.index', [
            'transactions' => $this->filtered()->with('category')->latest('occurred_on')->latest('id')->paginate(15),
            'income' => (int) ($totals['income']->total ?? 0),
            'expense' => (int) ($totals['expense']->total ?? 0),
            'count' => (int) $totals->sum('count'),
        ]);
    }
}
