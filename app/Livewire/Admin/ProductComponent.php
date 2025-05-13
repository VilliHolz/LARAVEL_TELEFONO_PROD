<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductComponent extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $barcode, $imei, $name, $purchase_price, $sale_price, $min_stock, $stock, $brand_id, $category_id, $branch_id, $image;
    public $product_id, $isOpen = false, $loading = false;

    protected $queryString = ['search'];
    protected $listeners = ['delete'];
    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'barcode' => [
                'required',
                Rule::unique('products')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->ignore($this->product_id)
            ],
            'imei' => [
                'nullable',
                Rule::unique('products')
                    ->where('branch_id', auth()->user()->branch->id)
                    ->ignore($this->product_id)
            ],
            'name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'min_stock' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|max:1024',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id'
        ];
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

        $brands = Brand::where('status', 'Activo')->get();
        $categories = Category::where('status', 'Activo')->get();

        return view('livewire.admin.product-component', compact('products', 'brands', 'categories'))
            ->extends('admin.layouts.app');
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
        $this->resetInputFields();
        $this->resetValidation();
        $this->isOpen = false;
    }

    public function resetInputFields()
    {
        $this->barcode = '';
        $this->imei = '';
        $this->name = '';
        $this->purchase_price = '';
        $this->sale_price = '';
        $this->min_stock = '';
        $this->stock = '';
        $this->brand_id = '';
        $this->category_id = '';
        $this->branch_id = '';
        $this->image = null;
        $this->product_id = null;
    }

    public function store()
    {
        $validatedData = $this->validate();

        $validatedData['branch_id'] = auth()->user()->branch->id;

        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
            $validatedData['image'] = $imagePath;
        }

        Product::updateOrCreate(
            ['id' => $this->product_id],
            $validatedData
        );

        session()->flash('message', $this->product_id ? 'Producto actualizado.' : 'Producto creado.');

        $this->closeModal();
        $this->resetInputFields();
    }


    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->product_id = $id;
        $this->barcode = $product->barcode;
        $this->imei = $product->imei;
        $this->name = $product->name;
        $this->purchase_price = $product->purchase_price;
        $this->sale_price = $product->sale_price;
        $this->min_stock = $product->min_stock;
        $this->stock = $product->stock;
        $this->brand_id = $product->brand_id;
        $this->category_id = $product->category_id;
        $this->branch_id = $product->branch_id;
        $this->image = $product->image;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($valor)
    {
        Product::find($valor['id'])->delete();

        session()->flash('message', 'Producto eliminado.');
    }
}
