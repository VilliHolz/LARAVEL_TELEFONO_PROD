<?php

namespace App\Livewire\Admin;

use App\Models\PaymentMethod;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentMethodComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $payment_method_id;
    public $isOpen = 0;

    protected $queryString = ['search'];

    protected $listeners = ['delete'];

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $payment_methods = PaymentMethod::where('name', 'like', $searchTerm)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.payment-method-component', compact('payment_methods'))->extends('admin.layouts.app');
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
        $this->payment_method_id = '';
    }

    public function store()
    {
        $rules = [
            'name' => 'required'
        ];        

        $this->validate($rules);

        PaymentMethod::updateOrCreate(['id' => $this->payment_method_id], [
            'name' => $this->name
        ]);

        session()->flash('message', $this->payment_method_id ? 'Forma Pago Actualizada.' : 'Forma Pago Creada.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $payment_method = PaymentMethod::findOrFail($id);
        $this->payment_method_id = $id;
        $this->name = $payment_method->name;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }


    public function delete($valor)
    {
        PaymentMethod::find($valor['id'])->delete();

        session()->flash('message', 'Forma Pago Eliminada.');
    }
}
