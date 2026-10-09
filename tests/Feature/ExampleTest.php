<?php

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\Person;
use App\Models\User;
use App\Support\Jalali;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk();
    }

    public function test_every_page_renders_for_a_signed_in_user(): void
    {
        $this->seed();
        $this->actingAs(User::first());
        $person = Person::create(['name' => 'Test Person']);

        foreach (['/', '/transactions', '/debts', '/people', "/people/{$person->id}", '/categories', '/reports', '/settings'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_a_debt_is_settled_once_payments_cover_it(): void
    {
        $person = Person::create(['name' => 'Ali']);
        $debt = Debt::create(['person_id' => $person->id, 'direction' => 'receivable', 'amount' => 1000, 'occurred_on' => today()]);

        $debt->payments()->create(['amount' => 400, 'paid_on' => today()]);
        $debt->syncSettlement();
        $this->assertNull($debt->fresh()->settled_at);
        $this->assertSame(600, $debt->fresh()->remaining);

        $debt->payments()->create(['amount' => 600, 'paid_on' => today()]);
        $debt->syncSettlement();
        $this->assertNotNull($debt->fresh()->settled_at);
    }

    public function test_jalali_dates_round_trip(): void
    {
        $date = Jalali::parse('۱۴۰۵/۰۷/۱۷');

        $this->assertSame('2026-10-09', $date->toDateString());
        $this->assertSame('1405/07/17', Jalali::format($date));
        $this->assertNull(Jalali::parse('1405/13/01'));
    }
}
