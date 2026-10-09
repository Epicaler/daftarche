<div class="space-y-5">
    <x-page-header title="گزارش‌ها" subtitle="تصویر کامل سال مالی‌ات به تفکیک ماه و دسته">
        <div class="flex gap-1 rounded-full bg-line/60 p-1">
            @foreach ($years as $y)
                <button type="button" wire:click="$set('year', {{ $y }})" @class(['chip', 'chip-active' => $year === $y])>{{ $y }}</button>
            @endforeach
        </div>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="درآمد سال" :value="money($totals['income'], false)" icon="trending-up" highlight :hint="'سال '.$year" />
        <x-stat-card label="هزینه سال" :value="money($totals['expense'], false)" icon="trending-down" :hint="'سال '.$year" />
        <x-stat-card label="پس‌انداز" :value="money($totals['net'], false)" icon="coins" :hint="$savingsRate !== null ? 'نرخ پس‌انداز '.$savingsRate.'٪' : 'درآمدی ثبت نشده'" />
        <x-stat-card label="میانگین هزینه ماهانه" :value="money($avgExpense, false)" icon="chart" hint="بر اساس ماه‌های دارای هزینه" />
    </div>

    <div class="card">
        <p class="card-title">درآمد و هزینه ماهانه {{ $year }}</p>
        <x-chart :config="['type' => 'trend', 'height' => 340, 'year' => $year] + $series" class="mt-3" />
    </div>

    <div class="grid gap-5 xl:grid-cols-2">
        <div class="card">
            <p class="card-title">هزینه‌ها بر اساس دسته</p>
            @if ($expenseCategories->isNotEmpty())
                <x-chart class="mt-3" :config="['type' => 'donut', 'height' => 300, 'title' => 'جمع هزینه', 'year' => $year, 'labels' => $expenseCategories->pluck('name'), 'values' => $expenseCategories->pluck('total'), 'colors' => $expenseCategories->pluck('color')]" />
            @else
                <x-empty-state icon="chart" title="داده‌ای برای این سال نیست" class="mt-4 border-0" />
            @endif
        </div>
        <div class="card">
            <p class="card-title">درآمدها بر اساس دسته</p>
            @if ($incomeCategories->isNotEmpty())
                <x-chart class="mt-3" :config="['type' => 'donut', 'height' => 300, 'title' => 'جمع درآمد', 'year' => $year, 'labels' => $incomeCategories->pluck('name'), 'values' => $incomeCategories->pluck('total'), 'colors' => $incomeCategories->pluck('color')]" />
            @else
                <x-empty-state icon="chart" title="داده‌ای برای این سال نیست" class="mt-4 border-0" />
            @endif
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-12">
        <div class="card xl:col-span-7">
            <p class="card-title">جدول ماهانه</p>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[480px] text-sm">
                    <thead>
                        <tr class="border-b border-line text-right text-xs text-muted">
                            <th class="pb-3 font-medium">ماه</th>
                            <th class="pb-3 font-medium">درآمد</th>
                            <th class="pb-3 font-medium">هزینه</th>
                            <th class="pb-3 font-medium">خالص</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($series['labels'] as $i => $label)
                            <tr>
                                <td class="py-2.5 font-medium">{{ \App\Support\Jalali::MONTHS[$i + 1] }}</td>
                                <td class="py-2.5">{{ money($series['income'][$i], false) }}</td>
                                <td class="py-2.5">{{ money($series['expense'][$i], false) }}</td>
                                <td class="py-2.5 font-semibold">
                                    <span @class(['inline-flex items-center gap-1', 'text-ink' => $net[$i] >= 0])>
                                        @if ($net[$i] !== 0)
                                            <x-icon :name="$net[$i] > 0 ? 'arrow-up' : 'arrow-down'" @class(['size-3.5', 'text-income' => $net[$i] > 0, 'text-expense' => $net[$i] < 0]) />
                                        @endif
                                        {{ money($net[$i], false) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-line font-bold">
                            <td class="pt-3">جمع</td>
                            <td class="pt-3">{{ money($totals['income'], false) }}</td>
                            <td class="pt-3">{{ money($totals['expense'], false) }}</td>
                            <td class="pt-3">{{ money($totals['net'], false) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card xl:col-span-5">
            <p class="card-title">بزرگ‌ترین هزینه‌های سال</p>
            <ul class="mt-3 space-y-1">
                @forelse ($topExpenses as $tx)
                    <li class="flex items-center gap-3 rounded-2xl px-2 py-2.5">
                        <span class="flex size-9 items-center justify-center rounded-full bg-surface text-xs font-bold text-muted">{{ $loop->iteration }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $tx->title }}</p>
                            <p class="truncate text-xs text-muted">{{ jdate_format($tx->occurred_on, 'j F') }}{{ $tx->category ? '، '.$tx->category->name : '' }}</p>
                        </div>
                        <span class="text-sm font-bold">{{ money($tx->amount) }}</span>
                    </li>
                @empty
                    <li class="py-8 text-center text-[13px] text-muted">هزینه‌ای ثبت نشده</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
