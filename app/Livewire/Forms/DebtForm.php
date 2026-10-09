<?php

namespace App\Livewire\Forms;

use App\Enums\DebtDirection;
use App\Models\Debt;
use App\Models\Person;
use App\Rules\JalaliDate;
use App\Support\Jalali;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class DebtForm extends Component
{
    public bool $open = false;

    public ?int $editingId = null;

    public string $direction = 'receivable';

    public string $person_name = '';

    public string $amount = '';

    public string $title = '';

    public string $date = '';

    public string $due_date = '';

    public string $description = '';

    #[On('open-debt-form')]
    public function openForm(?int $id = null, string $direction = 'receivable', ?int $person = null): void
    {
        $this->resetValidation();
        $this->reset('editingId', 'person_name', 'amount', 'title', 'due_date', 'description');
        $this->direction = DebtDirection::tryFrom($direction)?->value ?? 'receivable';
        $this->date = Jalali::today();
        $this->person_name = $person ? (string) Person::find($person)?->name : '';

        if ($id && $debt = Debt::with('person')->find($id)) {
            $this->editingId = $debt->id;
            $this->direction = $debt->direction->value;
            $this->person_name = $debt->person->name;
            $this->amount = (string) $debt->amount;
            $this->title = (string) $debt->title;
            $this->date = Jalali::format($debt->occurred_on);
            $this->due_date = Jalali::format($debt->due_on);
            $this->description = (string) $debt->description;
        }

        $this->open = true;
    }

    #[Computed]
    public function people()
    {
        return Person::orderBy('name')->pluck('name');
    }

    public function save(): void
    {
        $data = $this->validate([
            'direction' => ['required', Rule::enum(DebtDirection::class)],
            'person_name' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999999'],
            'title' => ['nullable', 'string', 'max:190'],
            'date' => ['required', new JalaliDate],
            'due_date' => ['nullable', new JalaliDate],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($data) {
            $person = Person::firstOrCreate(['name' => trim(preg_replace('/\s+/u', ' ', $data['person_name']))]);

            $debt = Debt::updateOrCreate(['id' => $this->editingId], [
                'person_id' => $person->id,
                'direction' => $data['direction'],
                'amount' => (int) $data['amount'],
                'title' => $data['title'] ?: null,
                'occurred_on' => Jalali::parse($data['date']),
                'due_on' => $data['due_date'] ? Jalali::parse($data['due_date']) : null,
                'description' => $data['description'] ?: null,
            ]);

            $debt->syncSettlement();
        });

        $label = DebtDirection::from($data['direction'])->label();
        $this->open = false;
        $this->dispatch('debt-saved');
        $this->dispatch('toast', message: $this->editingId ? "{$label} ویرایش شد" : "{$label} ثبت شد");
    }

    public function render()
    {
        return view('livewire.forms.debt-form');
    }
}
