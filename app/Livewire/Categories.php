<?php

namespace App\Livewire;

use App\Enums\TransactionType;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('دسته‌بندی‌ها')]
class Categories extends Component
{
    /** Validated categorical palette (fixed order). */
    public const COLORS = ['#2d8c5b', '#3b6fd8', '#c9761f', '#8b5cf6', '#d0473f', '#14a3a3', '#c2417f', '#a07a2a'];

    public ?int $editingId = null;

    public string $name = '';

    public string $type = 'expense';

    public string $color = '#2d8c5b';

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->type = $category->type->value;
        $this->color = $category->color;
        $this->resetValidation();
    }

    public function cancel(): void
    {
        $this->reset('editingId', 'name', 'color');
        $this->resetValidation();
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('categories')->where('type', $this->type)->ignore($this->editingId)],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'color' => ['required', Rule::in(self::COLORS)],
        ]);

        Category::updateOrCreate(['id' => $this->editingId], $data);
        $this->dispatch('toast', message: $this->editingId ? 'دسته‌بندی ویرایش شد' : 'دسته‌بندی اضافه شد');
        $this->cancel();
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'دسته‌بندی حذف شد');
    }

    public function render()
    {
        $categories = Category::withCount('transactions')->withSum('transactions', 'amount')->orderBy('name')->get()->groupBy(fn ($c) => $c->type->value);

        return view('livewire.categories', ['groups' => $categories]);
    }
}
