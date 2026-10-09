<?php

namespace App\Livewire\Concerns;

use App\Models\Debt;
use App\Models\DebtPayment;
use Livewire\Attributes\On;

trait ManagesDebts
{
    #[On('debt-saved')]
    public function refreshDebts(): void
    {
        //
    }

    /** Record a payment for whatever is left, which closes the debt. */
    public function settle(int $id): void
    {
        $debt = Debt::findOrFail($id);

        if ($debt->remaining > 0) {
            $debt->payments()->create(['amount' => $debt->remaining, 'paid_on' => today(), 'note' => 'تسویه کامل']);
        }

        $debt->syncSettlement();
        $this->dispatch('toast', message: 'حساب تسویه شد');
    }

    public function deleteDebt(int $id): void
    {
        Debt::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'حذف شد');
    }

    public function deletePayment(int $id): void
    {
        $payment = DebtPayment::findOrFail($id);
        $debt = $payment->debt;
        $payment->delete();
        $debt->syncSettlement();
        $this->dispatch('toast', message: 'پرداخت حذف شد');
    }
}
