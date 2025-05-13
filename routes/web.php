<?php

use App\Http\Controllers\Admin\ExcelExportController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\QuotesController;
use App\Http\Controllers\Admin\RepairController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SaleCreditController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['verify' => true]);

Route::middleware(['auth'])->group(function () {

    Route::get('/users', App\Livewire\Admin\UserComponent::class)->name('users.index')->middleware('can:leer usuarios');

    Route::get('/categories', App\Livewire\Admin\CategoryComponent::class)->name('categories.index')->middleware('can:leer categorias');

    Route::get('/brands', App\Livewire\Admin\BrandComponent::class)->name('brands.index')->middleware('can:leer marcas');

    Route::get('/branchs', App\Livewire\Admin\BranchComponent::class)->name('branchs.index')->middleware('can:leer sucursales');

    Route::get('/customers', App\Livewire\Admin\CustomerComponent::class)->name('customers.index')->middleware('can:leer clientes');

    Route::get('/suppliers', App\Livewire\Admin\SupplierComponent::class)->name('suppliers.index')->middleware('can:leer proveedores');

    Route::get('/products', App\Livewire\Admin\ProductComponent::class)->name('products.index')->middleware('can:leer productos');

    Route::get('/cashregisters', App\Livewire\Admin\CashRegisterComponent::class)->name('cashregisters.index')->middleware('can:leer cajas');

    Route::get('/cashregister/{encrypt_id}', App\Livewire\Admin\CloseBoxComponent::class)->name('cashregister.close')->middleware('can:cerrar cajas');

    Route::get('/sales/{encrypt_id?}', App\Livewire\Admin\SaleComponent::class)->name('sales.index')->middleware('can:crear ventas');

    Route::get('/sales-details', App\Livewire\Admin\SaleDetailComponent::class)->name('sales.details.index')->middleware('can:leer ventas');

    Route::get('/sales-credits', App\Livewire\Admin\SaleCreditComponent::class)->name('creditsales.index')->middleware('can:leer ventas a credito');

    Route::get('/payment-methods', App\Livewire\Admin\PaymentMethodComponent::class)->name('payment.methods.index')->middleware('can:leer formapagos');

    Route::get('/quotes', App\Livewire\Admin\QuotationComponent::class)->name('quotes.index')->middleware('can:leer cotizaciones');

    Route::get('/quotes-details', App\Livewire\Admin\QuotationDetailComponent::class)->name('quotes.details.index')->middleware('can:leer cotizaciones');

    Route::get('/purchase', App\Livewire\Admin\PurchaseComponent::class)->name('purchase.index')->middleware('can:leer compras');

    Route::get('/purchase-details', App\Livewire\Admin\PurchaseDetailComponent::class)->name('purchase.details.index')->middleware('can:leer compras');

    Route::get('/sales-credit-payments', App\Livewire\Admin\SaleCreditPaymentComponent::class)->name('sales.credit.payments.index')->middleware('can:pagos ventas a credito');

    Route::get('/repairs', App\Livewire\Admin\RepairComponent::class)->name('repairs.index')->middleware('can:leer reparaciones');
    Route::get('/repairs-edit/{encrypt_id}', App\Livewire\Admin\RepairEditComponent::class)->name('repairs.edit')->middleware('can:actualizar reparaciones');

    Route::get('/repairs-details-index', App\Livewire\Admin\RepairDetailComponent::class)->name('repairs.details.index')->middleware('can:leer reparaciones');

    Route::get('/repairs-list/{encrypt_id}', App\Livewire\Admin\ListRefaccionComponent::class)->name('repairs.list')->middleware('can:leer reparaciones');

    Route::get('/roles/assign/{id}', [RolController::class, 'assignRoles'])->name('roles.assign')->middleware('can:actualizar roles');

    Route::resource('roles', RolController::class)->middleware('can:leer roles');

    Route::put('roles/updateRoles/{id}', [RolController::class, 'updateRoles'])->name('roles.updateRoles')->middleware('can:actualizar roles');

    Route::get('/sale-ticket/{id}', [SaleController::class, 'generateTicket'])->name('sale.generate.ticket')->middleware('can:leer ventas');

    Route::get('/quotes-ticket/{id}', [QuotesController::class, 'generateTicket'])->name('quotes.generate.ticket')->middleware('can:leer cotizaciones');

    Route::get('/purchase-ticket/{id}', [PurchaseController::class, 'generateTicket'])->name('purchase.generate.ticket')->middleware('can:leer compras');

    Route::get('/repairs-ticket/{id}', [RepairController::class, 'generateTicket'])->name('repairs.generate.ticket')->middleware('can:leer reparaciones');

    Route::get('/sale-credit-ticket/{id}', [SaleCreditController::class, 'generateTicket'])->name('sale.credit.generate.ticket')->middleware('can:leer ventas a credito');

    Route::get('clients/export/{type}', [ExcelExportController::class, 'exportClients'])->name('exportClients');
    Route::get('products/export', [ExcelExportController::class, 'exportProducts'])->name('exportProducts')->middleware('can:reportes productos');
    Route::get('quotes/export', [ExcelExportController::class, 'exportQuotes'])->name('exportQuotes')->middleware('can:reportes cotizaciones');
    Route::get('sales/export', [ExcelExportController::class, 'exportSales'])->name('exportSales')->middleware('can:reportes ventas');
    Route::get('purchases/export', [ExcelExportController::class, 'exportPurchases'])->name('exportPurchases')->middleware('can:reportes compras');
    Route::get('repairs/export', [ExcelExportController::class, 'exportRepairs'])->name('exportRepairs')->middleware('can:reportes reparaciones');
    Route::get('boxs/export', [ExcelExportController::class, 'exportBoxs'])->name('exportBoxs')->middleware('can:reportes cajas');

    Route::post('/updateSucursal', [App\Http\Controllers\HomeController::class, 'updateSucursal'])->name('updateSucursal')->middleware('can:cambiar sucursales');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/perfil', [App\Http\Controllers\Admin\ProfileController::class, 'perfil'])->name('usuario.perfil');
    Route::put('profile', [App\Http\Controllers\Admin\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/update-password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});
