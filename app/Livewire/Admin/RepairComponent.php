<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use App\Models\CashRegister;
use App\Models\Contact;
use App\Models\Repair;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class RepairComponent extends Component
{
    use WithPagination;

    public $search = '';
    protected $queryString = ['search'];
    public $brands = [];
    public $isOpenCliente = 0;
    public $total = 0;
    public $name_client, $contact_id;
    public $model, $imei, $entry_date, $promised_date, $observations, $advance, $key, $pin, $status, $brand_id;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->entry_date = date('Y-m-d');
        $this->brands = Brand::where('status', 'Activo')->get();
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

    public function saveRepair()
    {
        $box = CashRegister::where('status', 'Activo')
            ->where('branch_id', auth()->user()->branch->id)
            ->first();

        if (!$box) return $this->flashError('La Caja está Cerrada');
        
        // Validación de los campos
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

        // Crear la reparación en la base de datos
        $repair = Repair::create([
            'model' => $this->model,
            'imei' => empty($this->imei) ? null : $this->imei,
            'entry_date' => $this->entry_date,
            'promised_date' => $this->promised_date,
            'observations' => empty($this->observations) ? null : $this->observations,
            'advance' => empty($this->advance) ? 0 : $this->advance,
            'key' => empty($this->key) ? null : $this->key,
            'pin' => empty($this->pin) ? null : $this->pin,
            'total' => $this->total,
            'brand_id' => $this->brand_id,
            'branch_id' => auth()->user()->branch->id,
            'user_id' => auth()->user()->id,
            'contact_id' => $this->contact_id,
            'cash_register_id' => $box->id
        ]);

        // Obtener el ID encriptado
        $repair_id_encrypted = Crypt::encrypt($repair->id);

        $this->reset('total', 'model', 'imei', 'entry_date', 'promised_date', 'observations', 'advance', 'model', 'pin', 'brand_id', 'contact_id');

        $this->dispatch('ticket-generated', ['url' => route('repairs.generate.ticket', $repair_id_encrypted)]);

        session()->flash('success', 'Orden realizada con éxito.');
    }

    private function flashError($message)
    {
        session()->flash('error', $message);
        return;
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        // Realizar la consulta con la paginación
        $contacts = Contact::where('name', 'like', $searchTerm)
            ->where('type', 'client')
            ->where('status', 'Activo')
            ->where('branch_id', auth()->user()->branch->id)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.repair-component', [
            'contacts' => $contacts
        ])->extends('admin.layouts.app');
    }
}
