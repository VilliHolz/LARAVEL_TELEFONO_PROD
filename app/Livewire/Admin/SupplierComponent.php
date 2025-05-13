<?php

namespace App\Livewire\Admin;

use App\Models\Contact;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierComponent extends Component
{
    use WithPagination;

    public $type = 'supplier';
    public $search = '';
    public $name, $phone, $email, $address, $contact_id;
    public $isOpen = 0;

    protected $queryString = ['search'];
    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                Rule::unique('contacts')
                    ->where('type', $this->type)
                    ->where('branch_id', auth()->user()->branch->id)
                    ->ignore($this->contact_id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('contacts')
                    ->where('type', $this->type)
                    ->where('branch_id', auth()->user()->branch->id)
                    ->ignore($this->contact_id),
            ],
            'address' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
        ];
    }


    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $contacts = Contact::where('name', 'like', $searchTerm)
            ->where('type', 'supplier')
            ->where('branch_id', auth()->user()->branch->id)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.supplier-component', compact('contacts'))
            ->extends('admin.layouts.app');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
        $this->contact_id = '';
    }

    public function store()
    {
        $validatedData = $this->validate();

        Contact::updateOrCreate(
            ['id' => $this->contact_id],
            array_merge($validatedData, [
                'branch_id' => auth()->user()->branch->id,
            ])
        );

        session()->flash('message', $this->contact_id ? 'Proveedor Actualizado.' : 'Proveedor Creado.');
        $this->closeModal();
        $this->resetInputFields();
    }


    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        $this->contact_id = $id;
        $this->name = $contact->name;
        $this->phone = $contact->phone;
        $this->email = $contact->email;
        $this->address = $contact->address;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($valor)
    {
        Contact::find($valor['id'])->delete();
        session()->flash('message', 'Proveedor Eliminado.');
    }
}
