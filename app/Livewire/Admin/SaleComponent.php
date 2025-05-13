<?php

namespace App\Livewire\Admin;

use App\Models\CashRegister;
use App\Models\Contact;
use App\Models\CreditSale;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class SaleComponent extends Component
{
    use WithPagination;

    public $cart = [];
    public $total = 0;
    public $paymentmethods = [];
    public $isOpenProcesar = 0;
    public $isOpenCliente = 0;
    public $name_client, $contact_id, $payment_method_id, $method;
    public $paid_with = 0;
    public $returned = 0;
    public $search = '';
    public $searchCustomer = '';
    protected $queryString = ['search', 'searchCustomer'];
    protected $paginationTheme = 'bootstrap';

    public function mount($encrypt_id = null)
    {
        if ($encrypt_id) {
            // Desencriptar el ID de la cotización
            $cotizacionId = Crypt::decrypt($encrypt_id);

            // Buscar la cotización y cargar sus productos (detalles)
            $cotizacionData = Quote::with('details.product', 'contact')->find($cotizacionId);

            if ($cotizacionData) {
                $this->contact_id = $cotizacionData->contact_id;
                $this->name_client = $cotizacionData->contact->name;
                // Agregar los productos de la cotización al carrito
                foreach ($cotizacionData->details as $detail) {
                    $this->cart[] = [
                        'id' => $detail->product->id,
                        'name' => $detail->product->name,
                        'price' => $detail->product->sale_price,
                        'quantity' => $detail->quantity,
                        'subtotal' => $detail->product->sale_price * $detail->quantity
                    ];
                }

                // Actualizar el total del carrito
                $this->updateTotal();
            }
        }

        // Configuración inicial
        $this->method = 'CONTADO';
        $this->paymentmethods = PaymentMethod::where('status', 'Activo')->get();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock < 1) {
            session()->flash('error', 'El producto no está disponible o no tiene suficiente stock.');
            return;
        }

        $exists = collect($this->cart)->firstWhere('id', $productId);

        if ($exists) {
            $this->incrementQuantity($productId);
        } else {
            $this->cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price,
                'quantity' => 1,
                'subtotal' => $product->sale_price
            ];
        }

        $this->updateTotal();
    }

    public function openModalProcesar()
    {
        $this->isOpenProcesar = true;
    }

    public function closeModalProcesar()
    {
        $this->isOpenProcesar = false;
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

    public function updatedPaidWith($value)
    {
        $this->returned = $value - $this->total;
    }

    public function updatedMethod($value)
    {
        if ($value === 'CREDITO') {
            $this->payment_method_id = '';
            $this->paid_with = 0;
            $this->returned = 0;
        }
    }

    public function incrementQuantity($productId)
    {
        foreach ($this->cart as &$item) {
            if ($item['id'] === $productId) {
                $product = Product::find($productId);

                if ($product->stock > $item['quantity']) {
                    $item['quantity']++;
                    $item['subtotal'] = $item['quantity'] * $item['price'];
                } else {
                    session()->flash('error', 'No hay suficiente stock para aumentar la cantidad.');
                }
                break;
            }
        }

        $this->updateTotal();
    }

    public function decrementQuantity($productId)
    {
        foreach ($this->cart as &$item) {
            if ($item['id'] === $productId) {
                if ($item['quantity'] > 1) {
                    $item['quantity']--;
                    $item['subtotal'] = $item['quantity'] * $item['price'];
                } else {
                    $this->removeFromCart($productId);
                }
                break;
            }
        }

        $this->updateTotal();
    }

    public function updatePrice($productId, $newPrice)
    {
        foreach ($this->cart as &$item) {
            if ($item['id'] === $productId) {
                if ($newPrice > 0) {
                    $item['price'] = $newPrice;
                    $item['subtotal'] = $item['quantity'] * $newPrice;
                } else {
                    session()->flash('error', 'El precio debe ser mayor a 0.');
                }
                break;
            }
        }

        $this->updateTotal();
    }

    public function removeFromCart($productId)
    {
        $this->cart = array_filter($this->cart, fn($item) => $item['id'] !== $productId);
        $this->updateTotal();
    }

    public function updateTotal()
    {
        $this->total = array_sum(array_column($this->cart, 'subtotal'));
    }

    public function saveSale()
    {
        $box = CashRegister::where('status', 'Activo')
            ->where('branch_id', Auth::user()->branch->id)
            ->first();

        if (!$box) return $this->flashError('La Caja está Cerrada');
        if (empty($this->cart)) return $this->flashError('El carrito está vacío.');
        if ($this->method === 'CREDITO' && empty($this->contact_id)) return $this->flashError('El Cliente es Requerido');
        if ($this->method === 'CONTADO' && empty($this->payment_method_id)) return $this->flashError('Seleccione una Forma de Pago');

        $sale = Sale::create([
            'total' => $this->total,
            'date' => date('Y-m-d'),
            'paid_with' => $this->paid_with ?: 0,
            'method' => $this->method,
            'payment_method_id' => $this->payment_method_id ?: null,
            'branch_id' => Auth::user()->branch->id,
            'user_id' => Auth::user()->id,
            'contact_id' => $this->contact_id ?: null,
            'cash_register_id' => $box->id,
        ]);

        foreach ($this->cart as $item) {
            $product = Product::find($item['id']);
            if ($product->stock < $item['quantity']) return $this->flashError("El producto {$product->name} no tiene suficiente stock.");

            SaleDetail::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
            $product->decrement('stock', $item['quantity']);
        }

        if ($this->method === 'CREDITO') {
            CreditSale::create(['amount' => $this->total, 'date' => now()->toDateString(), 'sale_id' => $sale->id]);
        }

        // Obtener el ID encriptado
        $sale_id_encrypted = Crypt::encrypt($sale->id);

        $this->reset('cart', 'total', 'paid_with', 'returned', 'method', 'payment_method_id');
        $this->closeModalProcesar();

        // Retornar la URL del ticket para la vista
        $this->dispatch('ticket-generated', ['url' => route('sale.generate.ticket', $sale_id_encrypted)]);
        session()->flash('success', 'Venta realizada con éxito.');
    }

    private function flashError($message)
    {
        session()->flash('error', $message);
        return;
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $searchTermCustomer = '%' . $this->searchCustomer . '%';

        $products = Product::with('brand', 'category')
            ->where('branch_id', Auth::user()->branch_id)
            ->where('status', 'Activo')
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('imei', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(9);

        $contacts = Contact::where('name', 'like', $searchTermCustomer)
            ->where('type', 'client')
            ->where('status', 'Activo')
            ->where('branch_id', Auth::user()->branch->id)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.sale-component', compact('products', 'contacts'))->extends('admin.layouts.app');
    }
}
