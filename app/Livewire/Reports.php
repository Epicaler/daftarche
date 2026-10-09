<?php

namespace App\Livewire;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Services\Ledger;
use App\Support\Jalali;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('گزارش‌ها')]
class Reports extends Component
{
    #[Url]
    public int $year = 0;

    public function mount(): void
    {
        $this->year = $this->year ?: Jalali::yearMonth()[0];
    }

    public function render(Ledger $ledger)
    {
        [$currentYear] = Jalali::yearMonth();
        $firstDate = Transaction::min('occurred_on');
        $firstYear = $firstDate ? Jalali::yearMonth(\Illuminate\Support\Carbon::parse($firstDate))[0] : $currentYear;

        [$from, $to] = Jalali::yearRange($this->year);
        $series = $ledger->monthlySeries(12, $this->year, 12);
        $totals = $ledger->totals($from, $to);
        $activeMonths = max(1, collect($series['expense'])->filter()->count());

        return view('livewire.reports', [
            'years' => range($currentYear, min($firstYear, $currentYear)),
            'series' => $series,
            'net' => array_map(fn ($i, $e) => $i - $e, $series['income'], $series['expense']),
            'totals' => $totals,
            'savingsRate' => $totals['income'] > 0 ? (int) round($totals['net'] / $totals['income'] * 100) : null,
            'avgExpense' => intdiv($totals['expense'], $activeMonths),
            'expenseCategories' => $ledger->categoryBreakdown(TransactionType::Expense, $from, $to),
            'incomeCategories' => $ledger->categoryBreakdown(TransactionType::Income, $from, $to),
            'topExpenses' => Transaction::expense()->between($from, $to)->with('category')->orderByDesc('amount')->limit(6)->get(),
        ]);
    }
}
