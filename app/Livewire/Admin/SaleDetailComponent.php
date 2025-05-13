<?php

namespace App\Livewire\Admin;

use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class SaleDetailComponent extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];
    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $sales = Sale::with(['contact'])->where('date', 'like', $searchTerm)
            ->where('status', 'Activo')
            ->where('branch_id', auth()->user()->branch->id)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.sale-detail-component', compact('sales'))
            ->extends('admin.layouts.app');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($valor)
    {
        Sale::find($valor['id'])->update(['status' => 'Inactivo']);

        session()->flash('message', 'Venta Anulado');
    }
}
