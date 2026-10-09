<?php

namespace App\Livewire;

use App\Enums\TransactionType;
use App\Models\Debt;
use App\Models\Person;
use App\Models\Transaction;
use App\Services\Ledger;
use App\Support\Jalali;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('داشبورد')]
class Dashboard extends Component
{
    #[On('transaction-saved')]
    #[On('debt-saved')]
    #[On('person-saved')]
    public function refreshStats(): void
    {
        //
    }

    public function render(Ledger $ledger)
    {
        [$jy, $jm] = Jalali::yearMonth();
        $month = $ledger->monthTotals($jy, $jm);
        $previous = $ledger->monthTotals(...Jalali::shiftMonth($jy, $jm, -1));
        $debts = $ledger->debtSummary();
        $balance = $ledger->balance();
        [$from, $to] = Jalali::monthRange($jy, $jm);

        $openDebts = Debt::open()->with('person')->withPaid()
            ->orderByRaw('due_on IS NULL')->orderBy('due_on')->latest('occurred_on')
            ->limit(5)->get();

        return view('livewire.dashboard', [
            'monthLabel' => Jalali::monthLabel($jy, $jm),
            'balance' => $balance,
            'month' => $month,
            'incomeTrend' => percent_change($month['income'], $previous['income']),
            'expenseTrend' => percent_change($month['expense'], $previous['expense']),
            'debts' => $debts,
            'netWorth' => $balance + $debts['receivable'] - $debts['payable'],
            'week' => $ledger->weekExpenses(),
            'series' => $ledger->monthlySeries(12),
            'categories' => $ledger->categoryBreakdown(TransactionType::Expense, $from, $to),
            'openDebts' => $openDebts,
            'reminder' => $openDebts->first(fn ($d) => $d->due_on !== null),
            'people' => Person::withBalances()->get()->filter(fn ($p) => $p->net_balance !== 0)->sortByDesc(fn ($p) => abs($p->net_balance))->take(4),
            'recent' => Transaction::with('category')->latest('occurred_on')->latest('id')->limit(6)->get(),
        ]);
    }
}
