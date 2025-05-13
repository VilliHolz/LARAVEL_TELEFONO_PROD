<?php

namespace App\Livewire\Admin;

use App\Models\Contact;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseComponent extends Component
{
    use WithPagination;

    public $cart = [];
    public $total = 0;
    public $paymentmethods = [];
    public $isOpenProcesar = 0;
    public $isOpenProveedor = 0;
    public $name_supplier, $contact_id;
    public $paid_with = 0;
    public $returned = 0;
    public $search = '';
    public $searchCustomer = '';
    protected $queryString = ['search', 'searchCustomer'];
    protected $paginationTheme = 'bootstrap';

    public function openModalProcesar()
    {
        $this->isOpenProcesar = true;
    }

    public function closeModalProcesar()
    {
        $this->isOpenProcesar = false;
    }

    public function openModalProveedor()
    {
        $this->isOpenProveedor = true;
    }

    public function closeModalProveedor()
    {
        $this->isOpenProveedor = false;
    }

    public function setSupplier($contact_id)
    {
        $contact = Contact::findOrFail($contact_id);
        $this->contact_id = $contact_id;
        $this->name_supplier = $contact->name;
        $this->closeModalProveedor();
    }

    public function updatedPaidWith($value)
    {
        $this->returned = $value - $this->total;
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        $exists = collect($this->cart)->firstWhere('id', $productId);

        if ($exists) {
            $this->incrementQuantity($productId);
        } else {
            $this->cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->purchase_price,
                'quantity' => 1,
                'subtotal' => $product->purchase_price
            ];
        }

        $this->updateTotal();
    }

    public function incrementQuantity($productId)
    {
        foreach ($this->cart as &$item) {
            if ($item['id'] === $productId) {
                $item['quantity']++;
                $item['subtotal'] = $item['quantity'] * $item['price'];
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

    public function savePurchase()
    {
        if (empty($this->cart)) return $this->flashError('El carrito está vacío.');
        if (empty($this->contact_id)) return $this->flashError('El Proveedor es Requerido');
        $purchase = Purchase::create([
            'total' => $this->total,
            'date' => date('Y-m-d'),
            'paid_with' => $this->paid_with ?: 0,
            'branch_id' => auth()->user()->branch->id,
            'user_id' => auth()->user()->id,
            'contact_id' => $this->contact_id
        ]);

        foreach ($this->cart as $item) {
            $product = Product::find($item['id']);
            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
            $product->increment('stock', $item['quantity']);
        }
        // Obtener el ID encriptado
        $purchase_id_encrypted = Crypt::encrypt($purchase->id);

        $this->reset('cart', 'total', 'paid_with', 'returned');
        $this->closeModalProcesar();

        // Retornar la URL del ticket para la vista
        $this->dispatch('ticket-generated', ['url' => route('purchase.generate.ticket', $purchase_id_encrypted)]);
        session()->flash('success', 'Compra realizada con éxito.');
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
            ->where('branch_id', auth()->user()->branch_id)
            ->where('status', 'Activo')
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('imei', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(9);

        $contacts = Contact::where('name', 'like', $searchTermCustomer)
            ->where('type', 'supplier')
            ->where('status', 'Activo')
            ->where('branch_id', auth()->user()->branch->id)
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('livewire.admin.purchase-component', compact('products', 'contacts'))->extends('admin.layouts.app');
    }
}
