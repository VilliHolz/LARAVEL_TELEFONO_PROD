<?php

namespace App\Livewire\Admin;

use App\Models\CashRegister;
use Livewire\Component;
use Livewire\WithPagination;

class CashRegisterComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $start_date, $start_time, $end_date, $initial_amount, $final_amount, $status, $user_id, $branch_id;
    public $cash_register_id, $isOpen = false, $loading = false;

    protected $queryString = ['search'];
    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'start_date' => 'required|string|max:255',
            'initial_amount' => 'required|numeric'
        ];
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $cashregisters = CashRegister::with('user', 'branch')
            ->where('start_date', 'like', $searchTerm)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.cash-register-component', compact('cashregisters'))
            ->extends('admin.layouts.app');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->start_date = date('Y-m-d');
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->resetInputFields();
        $this->resetValidation();
        $this->isOpen = false;
    }

    public function resetInputFields()
    {
        $this->start_date = '';
        $this->start_time = '';
        $this->end_date = '';
        $this->initial_amount = '';
        $this->final_amount = '';
        $this->status = '';
        $this->user_id = '';
        $this->branch_id = '';
        $this->cash_register_id = null;
    }

    public function store()
    {
        $validatedData = $this->validate();

        $validatedData['start_time'] = date('H:i:s');
        $validatedData['user_id'] = auth()->user()->id;
        $validatedData['branch_id'] = auth()->user()->branch->id;

        if (!$this->cash_register_id) {
            $box = CashRegister::where('status', 'Activo')
                ->where('branch_id', auth()->user()->branch->id)
                ->first();

            if ($box) {
                session()->flash('error', 'La caja ya está abierta');
                return;
            }
        }

        CashRegister::updateOrCreate(
            ['id' => $this->cash_register_id],
            $validatedData
        );

        session()->flash('message', $this->cash_register_id ? 'Caja actualizada.' : 'Caja creada.');

        $this->closeModal();
        $this->resetInputFields();
    }



    public function edit($id)
    {
        $cashregister = CashRegister::findOrFail($id);
        $this->cash_register_id = $id;
        $this->start_date = $cashregister->start_date;
        $this->start_time = $cashregister->start_time;
        $this->end_date = $cashregister->end_date;
        $this->initial_amount = $cashregister->initial_amount;
        $this->final_amount = $cashregister->final_amount;
        $this->status = $cashregister->status;
        $this->user_id = $cashregister->user_id;
        $this->branch_id = $cashregister->branch_id;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($valor)
    {
        CashRegister::find($valor['id'])->delete();

        session()->flash('message', 'Caja eliminado.');
    }
}
