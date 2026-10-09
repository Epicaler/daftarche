<?php

namespace App\Services;

use App\Enums\DebtDirection;
use App\Enums\TransactionType;
use App\Models\Debt;
use App\Models\Transaction;
use App\Support\Jalali;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Ledger
{
    public function balance(): int
    {
        $sums = Transaction::query()
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return (int) ($sums[TransactionType::Income->value] ?? 0) - (int) ($sums[TransactionType::Expense->value] ?? 0);
    }

    /** @return array{income:int, expense:int, net:int} */
    public function totals(CarbonInterface $from, CarbonInterface $to): array
    {
        $sums = Transaction::query()
            ->between($from, $to)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $income = (int) ($sums[TransactionType::Income->value] ?? 0);
        $expense = (int) ($sums[TransactionType::Expense->value] ?? 0);

        return ['income' => $income, 'expense' => $expense, 'net' => $income - $expense];
    }

    /** @return array{income:int, expense:int, net:int} */
    public function monthTotals(int $jy, int $jm): array
    {
        return $this->totals(...Jalali::monthRange($jy, $jm));
    }

    /**
     * Remaining (unpaid) receivables/payables plus the overall settlement ratio.
     *
     * @return array{receivable:int, payable:int, receivable_people:int, payable_people:int, total:int, settled:int, percent:int}
     */
    public function debtSummary(): array
    {
        $debts = Debt::query()->withPaid()->get(['id', 'person_id', 'direction', 'amount', 'settled_at']);
        $open = $debts->whereNull('settled_at');

        $receivable = $open->where('direction', DebtDirection::Receivable);
        $payable = $open->where('direction', DebtDirection::Payable);

        $total = (int) $debts->sum('amount');
        $settled = (int) $debts->sum(fn (Debt $d) => min($d->paid_amount, $d->amount));

        return [
            'receivable' => (int) $receivable->sum('remaining'),
            'payable' => (int) $payable->sum('remaining'),
            'receivable_people' => $receivable->pluck('person_id')->unique()->count(),
            'payable_people' => $payable->pluck('person_id')->unique()->count(),
            'total' => $total,
            'settled' => $settled,
            'percent' => $total > 0 ? (int) round($settled / $total * 100) : 0,
        ];
    }

    /**
     * Daily expenses for the current Saturday-to-Friday week.
     *
     * @return Collection<int, array{label:string, date:string, amount:int, today:bool, future:bool}>
     */
    public function weekExpenses(): Collection
    {
        $start = CarbonImmutable::today()->startOfWeek(CarbonInterface::SATURDAY);
        $end = $start->addDays(6);

        $sums = Transaction::query()->expense()->between($start, $end)
            ->selectRaw('occurred_on, SUM(amount) as total')
            ->groupBy('occurred_on')
            ->pluck('total', 'occurred_on')
            ->mapWithKeys(fn ($total, $date) => [substr($date, 0, 10) => (int) $total]);

        return collect(range(0, 6))->map(function (int $i) use ($start, $sums) {
            $day = $start->addDays($i);

            return [
                'label' => Jalali::WEEKDAYS_SHORT[$i],
                'date' => Jalali::format($day, 'l j F'),
                'amount' => $sums[$day->toDateString()] ?? 0,
                'today' => $day->isToday(),
                'future' => $day->isFuture(),
            ];
        });
    }

    /**
     * Income/expense per Jalali month.
     *
     * @return array{labels:list<string>, income:list<int>, expense:list<int>}
     */
    public function monthlySeries(int $count = 12, ?int $jy = null, ?int $jm = null): array
    {
        [$jy, $jm] = $jy ? [$jy, $jm] : Jalali::yearMonth();
        $months = collect(range($count - 1, 0))->map(fn ($back) => Jalali::shiftMonth($jy, $jm, -$back));

        $from = Jalali::monthRange(...$months->first())[0];
        $to = Jalali::monthRange(...$months->last())[1];

        $rows = Transaction::query()->between($from, $to)->get(['type', 'amount', 'occurred_on']);

        $series = ['labels' => [], 'income' => [], 'expense' => []];
        foreach ($months as [$y, $m]) {
            [$start, $end] = Jalali::monthRange($y, $m);
            $inMonth = $rows->filter(fn ($t) => $t->occurred_on->between($start, $end));

            $series['labels'][] = Jalali::MONTHS[$m];
            $series['income'][] = (int) $inMonth->where('type', TransactionType::Income)->sum('amount');
            $series['expense'][] = (int) $inMonth->where('type', TransactionType::Expense)->sum('amount');
        }

        return $series;
    }

    /** @return Collection<int, object{name:string, color:string, total:int}> */
    public function categoryBreakdown(TransactionType $type, CarbonInterface $from, CarbonInterface $to): Collection
    {
        return Transaction::query()
            ->leftJoin('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.type', $type->value)
            ->whereBetween('transactions.occurred_on', [$from->toDateString(), $to->toDateString()])
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get([
                DB::raw("COALESCE(categories.name, 'بدون دسته') as name"),
                DB::raw("COALESCE(categories.color, '#9aa5a0') as color"),
                DB::raw('SUM(transactions.amount) as total'),
            ])
            ->map(fn ($row) => (object) ['name' => $row->name, 'color' => $row->color, 'total' => (int) $row->total]);
    }
}
