<?php

namespace App\Livewire\People;

use App\Livewire\Concerns\ManagesDebts;
use App\Models\Person;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    use ManagesDebts;

    public Person $person;

    #[On('person-saved')]
    public function refreshPerson(): void
    {
        $this->person->refresh();
    }

    public function deletePerson()
    {
        $this->person->delete();
        session()->flash('toast', 'شخص و سوابقش حذف شد');

        return $this->redirectRoute('people', navigate: true);
    }

    public function render()
    {
        $person = Person::withBalances()->findOrFail($this->person->id);
        $debts = $this->person->debts()->with(['person', 'payments'])->withPaid()
            ->orderByRaw('settled_at IS NOT NULL')->latest('occurred_on')->get();

        return view('livewire.people.show', [
            'balances' => $person,
            'debts' => $debts,
            'totalReceivable' => $debts->filter->isReceivable()->sum('amount'),
            'totalPayable' => $debts->reject->isReceivable()->sum('amount'),
        ])->title($this->person->name);
    }
}
