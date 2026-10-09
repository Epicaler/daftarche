<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Debt;
use App\Models\Person;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Sample data for trying the dashboard: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);

        $income = Category::where('type', 'income')->pluck('id', 'name');
        $expense = Category::where('type', 'expense')->pluck('id', 'name');
        $today = CarbonImmutable::today();

        for ($m = 11; $m >= 0; $m--) {
            $base = $today->subMonthsNoOverflow($m)->startOfMonth();

            Transaction::create(['type' => 'income', 'amount' => 48_000_000 + random_int(0, 6) * 1_000_000, 'category_id' => $income['حقوق'], 'title' => 'حقوق ماهانه', 'occurred_on' => $base->addDays(random_int(0, 3))->min($today)]);
            if (random_int(0, 1)) {
                Transaction::create(['type' => 'income', 'amount' => random_int(5, 25) * 1_000_000, 'category_id' => $income['پروژه و فریلنس'], 'title' => 'پروژه طراحی سایت', 'occurred_on' => $base->addDays(random_int(8, 20))->min($today)]);
            }

            Transaction::create(['type' => 'expense', 'amount' => 18_000_000, 'category_id' => $expense['اجاره و قبوض'], 'title' => 'اجاره خانه', 'occurred_on' => $base->addDays(1)->min($today)]);

            foreach (range(1, 9) as $i) {
                [$title, $cat, $min, $max] = collect([
                    ['خرید سوپرمارکت', 'خوراک و سوپرمارکت', 800, 4500],
                    ['اسنپ', 'حمل و نقل', 120, 600],
                    ['کافه با دوستان', 'رستوران و کافه', 400, 1800],
                    ['خرید لباس', 'خرید و پوشاک', 1500, 6000],
                    ['داروخانه', 'سلامت و درمان', 200, 1500],
                    ['سینما', 'تفریح و سفر', 300, 900],
                    ['دوره آنلاین', 'آموزش', 900, 3000],
                ])->random();

                $date = $base->addDays(random_int(0, 29));
                if ($date->greaterThan($today)) {
                    continue;
                }

                Transaction::create(['type' => 'expense', 'amount' => random_int($min, $max) * 1000, 'category_id' => $expense[$cat], 'title' => $title, 'occurred_on' => $date]);
            }
        }

        // A few expenses in the current week so the weekly chart has shape.
        foreach (range(0, 6) as $d) {
            $date = $today->startOfWeek(CarbonImmutable::SATURDAY)->addDays($d);
            if ($date->lessThanOrEqualTo($today)) {
                Transaction::create(['type' => 'expense', 'amount' => random_int(300, 3500) * 1000, 'category_id' => $expense['خوراک و سوپرمارکت'], 'title' => 'خرید روزانه', 'occurred_on' => $date]);
            }
        }

        $people = collect(['علی رضایی', 'سارا محمدی', 'رضا کریمی', 'مریم احمدی', 'حسین نوری'])
            ->mapWithKeys(fn ($name) => [$name => Person::firstOrCreate(['name' => $name], ['phone' => '0912'.random_int(1000000, 9999999)])]);

        $debts = [
            ['علی رضایی', 'receivable', 15_000_000, 'قرض برای خرید گوشی', -40, 3, 5_000_000],
            ['سارا محمدی', 'receivable', 4_500_000, 'سهم سفر شمال', -25, -2, 0],
            ['رضا کریمی', 'payable', 8_000_000, 'قسط لپ‌تاپ', -60, 10, 2_000_000],
            ['مریم احمدی', 'receivable', 2_000_000, 'پول شام', -10, null, 0],
            ['حسین نوری', 'payable', 3_000_000, 'قرض کوتاه‌مدت', -90, -50, 3_000_000],
            ['علی رضایی', 'payable', 1_200_000, 'هزینه بلیت کنسرت', -5, 20, 0],
        ];

        foreach ($debts as [$name, $direction, $amount, $title, $ago, $due, $paid]) {
            $debt = Debt::create([
                'person_id' => $people[$name]->id, 'direction' => $direction, 'amount' => $amount, 'title' => $title,
                'occurred_on' => $today->addDays($ago), 'due_on' => $due === null ? null : $today->addDays($due),
            ]);
            if ($paid) {
                $debt->payments()->create(['amount' => $paid, 'paid_on' => $today->addDays($ago + 7), 'note' => 'کارت به کارت']);
            }
            $debt->syncSettlement();
        }
    }
}
