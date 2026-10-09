<?php

namespace App\Livewire\People;

use App\Models\Person;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('اشخاص')]
class Index extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $filter = '';

    #[On('person-saved')]
    #[On('debt-saved')]
    public function refreshPeople(): void
    {
        //
    }

    public function render()
    {
        $people = Person::query()
            ->withBalances()
            ->withCount('debts')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get()
            ->when($this->filter === 'owes-me', fn ($c) => $c->filter(fn ($p) => $p->net_balance > 0))
            ->when($this->filter === 'i-owe', fn ($c) => $c->filter(fn ($p) => $p->net_balance < 0))
            ->when($this->filter === 'clear', fn ($c) => $c->filter(fn ($p) => $p->net_balance === 0))
            ->sortByDesc(fn ($p) => abs($p->net_balance));

        return view('livewire.people.index', ['people' => $people]);
    }
}
