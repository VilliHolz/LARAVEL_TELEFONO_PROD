<?php

namespace App\Livewire\Admin;

use App\Models\CashRegister;
use App\Models\CreditSale;
use App\Models\PaymentSale;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class SaleCreditComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $creditSaleId;
    public $abonoAmount;
    public $isOpen = 0;

    protected $queryString = ['search'];
    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';

    public function openModal($creditSaleId)
    {
        $this->creditSaleId = $creditSaleId;
        $this->abonoAmount = 0;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    // Método para agregar el abono
    public function addAbono()
    {
        $this->validate([
            'abonoAmount' => 'required|numeric|min:0.01',
        ]);

        $box = CashRegister::where('status', 'Activo')
            ->where('branch_id', auth()->user()->branch->id)
            ->first();
        if (!$box) return $this->flashError('La Caja está Cerrada');

        // Buscar el crédito
        $creditSale = CreditSale::find($this->creditSaleId);
        if (!$creditSale) {
            session()->flash('error', 'No se encontró la venta de crédito.');
            return;
        }

        // Verificar si el abono no excede el monto pendiente
        $totalPayments = $creditSale->payments->sum('amount');
        $pendingAmount = $creditSale->amount - $totalPayments;

        if ($this->abonoAmount > $pendingAmount) {
            session()->flash('error', 'El monto del abono excede el saldo pendiente.');
            return;
        }

        // Crear el registro de pago
        PaymentSale::create([
            'credit_id' => $creditSale->id,
            'amount' => $this->abonoAmount,
            'date' => date('Y-m-d'),
            'cash_register_id' => $box->id,
            'user_id' => auth()->user()->id
        ]);

        $payment_id_encrypted = Crypt::encrypt($creditSale->id);

        $this->dispatch('ticket-generated', ['url' => route('sale.credit.generate.ticket', $payment_id_encrypted)]);

        session()->flash('success', 'Abono agregado correctamente.');
        $this->isOpen = false;
        // Limpiar el formulario
        $this->reset(['abonoAmount', 'creditSaleId']);
    }

    private function flashError($message)
    {
        session()->flash('error', $message);
        return;
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $creditsales = CreditSale::with(['sale', 'payments']) // Cargar pagos junto con la venta
            ->where('date', 'like', $searchTerm)
            ->whereHas('sale', function ($query) {
                $query->where('branch_id', auth()->user()->branch->id);
            })
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.sale-credit-component', compact('creditsales'))
            ->extends('admin.layouts.app');
    }


    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($valor)
    {
        CreditSale::find($valor['id'])->update(['status' => 'Inactivo']);

        session()->flash('message', 'Venta Anulado');
    }
}
