<?php

namespace App\Livewire\Admin;

use App\Models\Repair;
use Livewire\Component;
use Livewire\WithPagination;

class RepairDetailComponent extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];
    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $repairs = Repair::with(['brand', 'contact', 'user', 'branch'])
            ->where('entry_date', 'like', $searchTerm)
            ->where('branch_id', auth()->user()->branch->id)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.repair-detail-component', compact('repairs'))
            ->extends('admin.layouts.app');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($valor)
    {
        Repair::find($valor['id'])->update(['status' => 'Cancelado']);
        session()->flash('message', 'Orden Cancelado');
    }
}
