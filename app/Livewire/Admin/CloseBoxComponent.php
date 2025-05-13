<?php

namespace App\Livewire\Admin;

use App\Models\CashRegister;
use App\Models\PaymentSale;
use App\Models\Repair;
use App\Models\Sale;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class CloseBoxComponent extends Component
{
    public $box;
    public $totalContado = 0;
    public $totalAbonos = 0;
    public $totalTaller = 0;

    protected $listeners = ['close'];

    public function mount($encrypt_id)
    {
        $box_id = Crypt::decrypt($encrypt_id);
        $this->box = CashRegister::with('user')->find($box_id);

        // Calcular el total de ventas al contado para esta caja
        $this->totalContado = Sale::where('cash_register_id', $box_id)
            ->where('method', 'Contado')
            ->where('status', 'Activo')
            ->sum('total');

        // Calcular el total de abonos a créditos para esta caja
        $this->totalAbonos = PaymentSale::where('cash_register_id', $box_id)
            ->sum('amount');

        $this->totalTaller = Repair::where('cash_register_id', $box_id)
            ->whereNot('status', 'Cancelado')
            ->sum('total');
    }

    public function confirmClose()
    {
        $this->dispatch('show-close-confirmation');
    }

    public function close()
    {
        $total = $this->totalAbonos + $this->totalContado + $this->totalTaller;
        if ($total <= 0) {
            session()->flash('error', 'El monto esta cerro.');
            return;
        }
        $this->box->status = 'Cerrado';
        $this->box->end_date = date('Y-m-d H:i:s');
        $this->box->final_amount = $total;
        $this->box->save();
        session()->flash('message', 'Caja Cerado.');
    }

    public function render()
    {
        return view('livewire.admin.close-box-component')->extends('admin.layouts.app');
    }
}
