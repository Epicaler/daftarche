<?php

namespace App\Livewire\Forms;

use App\Models\Person;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class PersonForm extends Component
{
    public bool $open = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $phone = '';

    public string $note = '';

    #[On('open-person-form')]
    public function openForm(?int $id = null): void
    {
        $this->resetValidation();
        $this->reset('editingId', 'name', 'phone', 'note');

        if ($id && $person = Person::find($id)) {
            $this->editingId = $person->id;
            $this->name = $person->name;
            $this->phone = (string) $person->phone;
            $this->note = (string) $person->note;
        }

        $this->open = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('people', 'name')->ignore($this->editingId)],
            'phone' => ['nullable', 'string', 'max:32'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        Person::updateOrCreate(['id' => $this->editingId], [
            'name' => trim($data['name']),
            'phone' => $data['phone'] ?: null,
            'note' => $data['note'] ?: null,
        ]);

        $this->open = false;
        $this->dispatch('person-saved');
        $this->dispatch('toast', message: $this->editingId ? 'اطلاعات شخص ویرایش شد' : 'شخص جدید اضافه شد');
    }

    public function render()
    {
        return view('livewire.forms.person-form');
    }
}
