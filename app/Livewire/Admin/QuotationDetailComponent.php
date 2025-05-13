<?php

namespace App\Livewire\Admin;

use App\Models\Quote;
use Livewire\Component;
use Livewire\WithPagination;

class QuotationDetailComponent extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];
    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $quotes = Quote::with(['contact'])->where('date', 'like', $searchTerm)
            ->where('branch_id', auth()->user()->branch->id)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.quotation-detail-component', compact('quotes'))
            ->extends('admin.layouts.app');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($valor)
    {
        Quote::find($valor['id'])->delete();

        session()->flash('message', 'Cotización Anulado');
    }
}
