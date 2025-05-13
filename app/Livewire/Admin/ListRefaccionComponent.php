<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Repair;
use App\Models\RepairDetail;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class ListRefaccionComponent extends Component
{
    use WithPagination;

    public $repair;
    public $cart = [];
    public $total = 0;
    public $search = '';
    protected $queryString = ['search'];
    protected $paginationTheme = 'bootstrap';

    public function mount($encrypt_id)
    {
        // Desencriptar ID de la reparación
        $repair_id = Crypt::decrypt($encrypt_id);

        // Cargar reparación con relaciones
        $this->repair = Repair::with('brand', 'contact')->find($repair_id);

        // Recuperar detalles de repuestos asociados y agregar al carrito
        $repairDetails = RepairDetail::where('repair_id', $repair_id)->with('product')->get();

        foreach ($repairDetails as $detail) {
            $this->cart[] = [
                'id' => $detail->product->id,
                'name' => $detail->product->name,
                'price' => $detail->price,
                'quantity' => $detail->quantity,
                'subtotal' => $detail->price * $detail->quantity,
            ];
        }

        $this->updateTotal();
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
                'price' => $product->sale_price,
                'quantity' => 1,
                'subtotal' => $product->sale_price,
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

    public function save()
    {
        if (empty($this->cart)) {
            return session()->flash('error', 'El carrito está vacío.');
        }

        // Obtener los IDs de productos en el carrito
        $cartProductIds = collect($this->cart)->pluck('id')->toArray();

        // Eliminar de la base de datos los repuestos que ya no están en el carrito
        RepairDetail::where('repair_id', $this->repair->id)
            ->whereNotIn('product_id', $cartProductIds)
            ->delete();

        // Guardar o actualizar los repuestos en la base de datos
        foreach ($this->cart as $item) {
            RepairDetail::updateOrCreate(
                [
                    'repair_id' => $this->repair->id,
                    'product_id' => $item['id'],
                ],
                [
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]
            );
        }

        // Resetear el carrito para reflejar los cambios guardados
        $this->reset('cart');

        // Volver a cargar los detalles actualizados en el carrito
        $this->mount(Crypt::encrypt($this->repair->id));

        session()->flash('success', 'Repuestos actualizados con éxito.');
    }

    public function changeStatus($repairId, $newStatus)
    {
        $repair = Repair::find($repairId);
        $repair->status = $newStatus;
        $repair->save();
        $this->repair->status = $newStatus;
        session()->flash('success', 'Estado actualizado correctamente.');
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

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

        return view('livewire.admin.list-refaccion-component', compact('products'))->extends('admin.layouts.app');
    }
}
