<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Transaction;
use App\Support\Jalali;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __invoke(string $type): StreamedResponse
    {
        abort_unless(in_array($type, ['transactions', 'debts']), 404);

        $filename = $type.'-'.str_replace('/', '-', Jalali::today()).'.csv';

        return response()->streamDownload(function () use ($type) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel renders Persian correctly

            $type === 'transactions' ? $this->transactions($out) : $this->debts($out);

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function transactions($out): void
    {
        fputcsv($out, ['تاریخ', 'نوع', 'عنوان', 'دسته‌بندی', 'مبلغ', 'توضیحات']);

        Transaction::with('category')->orderBy('occurred_on')->orderBy('id')->chunk(500, function ($rows) use ($out) {
            foreach ($rows as $t) {
                fputcsv($out, [Jalali::format($t->occurred_on), $t->type->label(), $t->title, $t->category?->name, $t->amount, $t->description]);
            }
        });
    }

    private function debts($out): void
    {
        fputcsv($out, ['تاریخ', 'نوع', 'شخص', 'بابت', 'مبلغ', 'پرداخت شده', 'مانده', 'سررسید', 'وضعیت', 'توضیحات']);

        Debt::with('person')->withPaid()->orderBy('occurred_on')->chunk(500, function ($rows) use ($out) {
            foreach ($rows as $d) {
                fputcsv($out, [
                    Jalali::format($d->occurred_on), $d->direction->label(), $d->person->name, $d->title, $d->amount,
                    $d->paid_amount, $d->remaining, Jalali::format($d->due_on), $d->settled_at ? 'تسویه' : 'باز', $d->description,
                ]);
            }
        });
    }
}
