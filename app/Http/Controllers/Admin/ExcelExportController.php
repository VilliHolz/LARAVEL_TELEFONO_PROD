<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CashRegistersExport;
use App\Exports\ContactsExport;
use App\Exports\ProductsExport;
use App\Exports\PurchasesExport;
use App\Exports\QuotesExport;
use App\Exports\RepairsExport;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportController extends Controller
{
    public function exportClients($type)
    {
        if ($type === 'client') {
            $this->authorize('reportes clientes');
        } elseif ($type === 'proveedores') {
            $this->authorize('reportes proveedores');
        } else {
            abort(403, 'Acción no autorizada');
        }
        $empresa = Branch::find(auth()->user()->branch_id);
        return Excel::download(new ContactsExport($empresa, $type), 'contacts.xlsx');
    }


    public function exportProducts()
    {
        $empresa = Branch::find(auth()->user()->branch_id);
        return Excel::download(new ProductsExport($empresa), 'productos.xlsx');
    }

    public function exportQuotes()
    {
        $empresa = Branch::find(auth()->user()->branch_id);
        return Excel::download(new QuotesExport($empresa), 'cotizaciones.xlsx');
    }

    public function exportSales()
    {
        $empresa = Branch::find(auth()->user()->branch_id);
        return Excel::download(new SalesExport($empresa), 'ventas.xlsx');
    }

    public function exportPurchases()
    {
        $empresa = Branch::find(auth()->user()->branch_id);
        return Excel::download(new PurchasesExport($empresa), 'compras.xlsx');
    }

    public function exportRepairs()
    {
        $empresa = Branch::find(auth()->user()->branch_id);
        return Excel::download(new RepairsExport($empresa), 'ordenes.xlsx');
    }

    public function exportBoxs()
    {
        $empresa = Branch::find(auth()->user()->branch_id);
        return Excel::download(new CashRegistersExport($empresa), 'cajas.xlsx');
    }
}
