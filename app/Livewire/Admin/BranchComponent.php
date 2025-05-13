<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;

class BranchComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $phone, $address, $email, $representative, $message, $status, $branch_id;
    public $isOpen = 0;

    protected $queryString = ['search'];

    protected $listeners = ['delete'];

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $branches = Branch::where('name', 'like', $searchTerm)
            ->orWhere('phone', 'like', $searchTerm)
            ->orWhere('address', 'like', $searchTerm)
            ->orWhere('email', 'like', $searchTerm)
            ->orWhere('representative', 'like', $searchTerm)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.branch-component', compact('branches'))->extends('admin.layouts.app');
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
        $this->address = '';
        $this->email = '';
        $this->message = '';
        $this->representative = '';
        $this->status = '';
        $this->branch_id = '';
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required|unique:branches,phone,' . $this->branch_id,  // El phone es opcional, pero único si se ingresa
            'address' => 'required',
            'email' => 'required|email|unique:branches,email,' . $this->branch_id,  // validación de email único
            'message' => 'nullable|string',
            'representative' => 'nullable|string',
            'status' => 'required|in:Activo,Inactivo',
        ];

        $this->validate($rules);

        Branch::updateOrCreate(['id' => $this->branch_id], [
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'email' => $this->email,
            'message' => empty($this->message) ? null : $this->message,
            'representative' => empty($this->representative) ? null : $this->representative,
            'status' => $this->status,
        ]);

        session()->flash('message', $this->branch_id ? 'Sucursal Actualizada.' : 'Sucursal Creada.');

        $this->closeModal();
        $this->resetInputFields();
    }


    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        $this->branch_id = $id;
        $this->name = $branch->name;
        $this->phone = $branch->phone;
        $this->address = $branch->address;
        $this->email = $branch->email;
        $this->message = $branch->message;
        $this->representative = $branch->representative;
        $this->status = $branch->status;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($valor)
    {
        Branch::find($valor['id'])->delete();

        session()->flash('message', 'Sucursal Eliminada.');
    }
}
