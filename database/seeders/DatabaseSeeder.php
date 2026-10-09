<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Idempotent: safe to run on every container start.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        if (! User::where('email', $email)->exists() && User::count() === 0) {
            User::create([
                'name' => env('ADMIN_NAME', 'مدیر'),
                'email' => $email,
                'password' => env('ADMIN_PASSWORD', 'password'),
            ]);
        }

        if (Category::count() === 0) {
            $defaults = [
                'income' => [['حقوق', '#2d8c5b'], ['پروژه و فریلنس', '#3b6fd8'], ['سود و سرمایه‌گذاری', '#c9761f'], ['هدیه', '#8b5cf6']],
                'expense' => [
                    ['خوراک و سوپرمارکت', '#2d8c5b'], ['اجاره و قبوض', '#3b6fd8'], ['حمل و نقل', '#c9761f'], ['رستوران و کافه', '#8b5cf6'],
                    ['خرید و پوشاک', '#d0473f'], ['سلامت و درمان', '#14a3a3'], ['تفریح و سفر', '#c2417f'], ['آموزش', '#a07a2a'],
                ],
            ];

            foreach ($defaults as $type => $items) {
                foreach ($items as [$name, $color]) {
                    Category::create(compact('name', 'type', 'color'));
                }
            }
        }
    }
}
