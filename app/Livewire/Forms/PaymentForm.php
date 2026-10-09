<?php

namespace App\Livewire\Forms;

use App\Models\Debt;
use App\Rules\JalaliDate;
use App\Support\Jalali;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentForm extends Component
{
    public bool $open = false;

    public ?int $debtId = null;

    public string $amount = '';

    public string $date = '';

    public string $note = '';

    #[On('open-payment-form')]
    public function openForm(int $debt): void
    {
        $this->resetValidation();
        $model = Debt::find($debt);

        if (! $model) {
            return;
        }

        $this->debtId = $model->id;
        $this->amount = (string) $model->remaining;
        $this->date = Jalali::today();
        $this->note = '';
        $this->open = true;
    }

    public function getDebtProperty(): ?Debt
    {
        return $this->debtId ? Debt::with('person')->withPaid()->find($this->debtId) : null;
    }

    public function save(): void
    {
        $debt = $this->debt;
        abort_unless($debt, 404);

        $data = $this->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:'.max(1, $debt->remaining)],
            'date' => ['required', new JalaliDate],
            'note' => ['nullable', 'string', 'max:190'],
        ], [
            'amount.max' => 'مبلغ پرداخت نمی‌تواند از مانده ('.money($debt->remaining).') بیشتر باشد.',
        ]);

        $debt->payments()->create([
            'amount' => (int) $data['amount'],
            'paid_on' => Jalali::parse($data['date']),
            'note' => $data['note'] ?: null,
        ]);
        $debt->syncSettlement();

        $this->open = false;
        $this->dispatch('debt-saved');
        $this->dispatch('toast', message: $debt->fresh()->settled_at ? 'پرداخت ثبت شد و حساب تسویه شد' : 'پرداخت ثبت شد');
    }

    public function render()
    {
        return view('livewire.forms.payment-form', ['debt' => $this->debt]);
    }
}
