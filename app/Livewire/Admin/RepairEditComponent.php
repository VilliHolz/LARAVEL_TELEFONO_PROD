<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use App\Models\CashRegister;
use App\Models\Contact;
use App\Models\Repair;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class RepairEditComponent extends Component
{
    use WithPagination;

    public $search = '';
    protected $queryString = ['search'];
    public $brands = [];
    public $isOpenCliente = false;
    public $total = 0;
    public $name_client, $contact_id;
    public $model, $imei, $entry_date, $promised_date, $observations, $advance, $key, $pin, $status, $brand_id, $repair_id;
    protected $paginationTheme = 'bootstrap';
    public $repair;

    public function mount($encrypt_id)
    {
        $this->repair_id = Crypt::decrypt($encrypt_id);
        $this->repair = Repair::with('contact')->findOrFail($this->repair_id);

        $this->model = $this->repair->model;
        $this->imei = $this->repair->imei;
        $this->entry_date = $this->repair->entry_date;
        $this->promised_date = $this->repair->promised_date;
        $this->observations = $this->repair->observations;
        $this->advance = $this->repair->advance;
        $this->key = $this->repair->key;
        $this->pin = $this->repair->pin;
        $this->status = $this->repair->status;
        $this->brand_id = $this->repair->brand_id;
        $this->total = $this->repair->total;
        $this->name_client = $this->repair->contact->name;
        $this->contact_id = $this->repair->contact_id;

        $this->brands = Brand::where('status', 'Activo')->get();
    }

    public function saveUpdate()
    {
        $box = CashRegister::where('status', 'Activo')
            ->where('branch_id', auth()->user()->branch->id)
            ->first();

        if (!$box) return $this->flashError('La Caja está Cerrada');

        $this->validate([
            'model' => 'required|string|max:255',
            'imei' => 'nullable|string|max:255',
            'entry_date' => 'required|date',
            'promised_date' => 'required|date|after_or_equal:entry_date',
            'observations' => 'nullable|string',
            'advance' => 'nullable|numeric|min:0',
            'key' => 'nullable|string|max:255',
            'pin' => 'nullable|string|max:255',
            'total' => 'required|numeric|min:0',
            'brand_id' => 'required|exists:brands,id',
            'contact_id' => 'required|exists:contacts,id',
        ]);

        $this->repair->update([
            'model' => $this->model,
            'imei' => $this->imei ?: null,
            'entry_date' => $this->entry_date,
            'promised_date' => $this->promised_date,
            'observations' => $this->observations ?: null,
            'advance' => $this->advance ?: 0,
            'key' => $this->key ?: null,
            'pin' => $this->pin ?: null,
            'total' => $this->total,
            'brand_id' => $this->brand_id,
            'branch_id' => auth()->user()->branch->id,
            'user_id' => auth()->user()->id,
            'contact_id' => $this->contact_id,
            'cash_register_id' => $box->id
        ]);

        $repair_id_encrypted = Crypt::encrypt($this->repair->id);

        $this->dispatch('ticket-generated', ['url' => route('repairs.generate.ticket', $repair_id_encrypted)]);

        session()->flash('success', 'Orden actualizada con éxito.');
    }

    private function flashError($message)
    {
        session()->flash('error', $message);
        return false;
    }

    public function openModalCliente()
    {
        $this->isOpenCliente = true;
    }

    public function closeModalCliente()
    {
        $this->isOpenCliente = false;
    }

    public function setClient($contact_id)
    {
        $contact = Contact::findOrFail($contact_id);
        $this->contact_id = $contact_id;
        $this->name_client = $contact->name;
        $this->closeModalCliente();
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $contacts = Contact::where('name', 'like', $searchTerm)
            ->where('type', 'client')
            ->where('status', 'Activo')
            ->where('branch_id', auth()->user()->branch->id)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.repair-edit-component', compact('contacts'))
            ->extends('admin.layouts.app');
    }
}