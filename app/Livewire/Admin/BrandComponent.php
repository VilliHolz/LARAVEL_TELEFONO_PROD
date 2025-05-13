<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;

class BrandComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $brand_id;
    public $isOpen = 0;

    protected $queryString = ['search'];

    protected $listeners = ['delete'];

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $brands = Brand::where('name', 'like', $searchTerm)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.brand-component', compact('brands'))->extends('admin.layouts.app');
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
        $this->brand_id = '';
    }

    public function store()
    {
        $rules = [
            'name' => 'required'
        ];        

        $this->validate($rules);

        Brand::updateOrCreate(['id' => $this->brand_id], [
            'name' => $this->name
        ]);

        session()->flash('message', $this->brand_id ? 'Marca Actualizada.' : 'Marca Creada.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        $this->brand_id = $id;
        $this->name = $brand->name;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }


    public function delete($valor)
    {
        Brand::find($valor['id'])->delete();

        session()->flash('message', 'Marca Eliminada.');
    }
}
