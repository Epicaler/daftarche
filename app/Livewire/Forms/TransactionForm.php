<?php

namespace App\Livewire\Forms;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Transaction;
use App\Rules\JalaliDate;
use App\Support\Jalali;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class TransactionForm extends Component
{
    public bool $open = false;

    public ?int $editingId = null;

    public string $type = 'expense';

    public string $amount = '';

    public ?int $category_id = null;

    public string $title = '';

    public string $date = '';

    public string $description = '';

    #[On('open-transaction-form')]
    public function openForm(?int $id = null, string $type = 'expense'): void
    {
        $this->resetValidation();
        $this->reset('editingId', 'amount', 'category_id', 'title', 'description');
        $this->type = TransactionType::tryFrom($type)?->value ?? 'expense';
        $this->date = Jalali::today();

        if ($id && $transaction = Transaction::find($id)) {
            $this->editingId = $transaction->id;
            $this->type = $transaction->type->value;
            $this->amount = (string) $transaction->amount;
            $this->category_id = $transaction->category_id;
            $this->title = $transaction->title;
            $this->date = Jalali::format($transaction->occurred_on);
            $this->description = (string) $transaction->description;
        }

        $this->open = true;
    }

    public function updatedType(): void
    {
        $this->category_id = null;
    }

    #[Computed]
    public function categories()
    {
        return Category::ofType($this->type)->orderBy('name')->get();
    }

    public function save(): void
    {
        $data = $this->validate([
            'type' => ['required', Rule::enum(TransactionType::class)],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999999'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('type', $this->type)],
            'title' => ['required', 'string', 'max:190'],
            'date' => ['required', new JalaliDate],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        Transaction::updateOrCreate(['id' => $this->editingId], [
            'type' => $data['type'],
            'amount' => (int) $data['amount'],
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'occurred_on' => Jalali::parse($data['date']),
            'description' => $data['description'] ?: null,
        ]);

        $this->open = false;
        $this->dispatch('transaction-saved');
        $this->dispatch('toast', message: $this->editingId ? 'تراکنش ویرایش شد' : 'تراکنش ثبت شد');
    }

    public function render()
    {
        return view('livewire.forms.transaction-form');
    }
}
