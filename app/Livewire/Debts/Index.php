<?php

namespace App\Livewire\Debts;

use App\Livewire\Concerns\ManagesDebts;
use App\Models\Debt;
use App\Services\Ledger;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('طلب و بدهی')]
class Index extends Component
{
    use ManagesDebts, WithPagination;

    #[Url]
    public string $direction = '';

    #[Url]
    public string $status = 'open';

    #[Url]
    public string $search = '';

    public function updated($property): void
    {
        if (in_array($property, ['direction', 'status', 'search'])) {
            $this->resetPage();
        }
    }

    public function render(Ledger $ledger)
    {
        $debts = Debt::query()
            ->with(['person', 'payments'])
            ->withPaid()
            ->when($this->direction, fn ($q) => $q->direction($this->direction))
            ->when($this->status === 'open', fn ($q) => $q->open())
            ->when($this->status === 'settled', fn ($q) => $q->whereNotNull('settled_at'))
            ->when($this->status === 'overdue', fn ($q) => $q->open()->whereDate('due_on', '<', today()))
            ->when($this->search, fn ($q) => $q->where(fn ($q) => $q
                ->where('title', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%")
                ->orWhereHas('person', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))))
            ->orderByRaw('settled_at IS NOT NULL')
            ->orderByRaw('due_on IS NULL')
            ->orderBy('due_on')
            ->latest('occurred_on')
            ->paginate(12);

        return view('livewire.debts.index', [
            'debts' => $debts,
            'summary' => $ledger->debtSummary(),
            'overdue' => Debt::open()->whereDate('due_on', '<', today())->count(),
        ]);
    }
}
