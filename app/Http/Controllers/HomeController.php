<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Purchase;
use App\Models\Repair;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $currentYear = now()->year;

        // Ventas activas por mes
        $salesData = Sale::where('status', 'Activo')
            ->where('branch_id', auth()->user()->branch_id)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Compras activas por mes
        $purchasesData = Purchase::where('status', 'Activo')
            ->where('branch_id', auth()->user()->branch_id)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Reparaciones que no estén canceladas
        $repairsData = Repair::where('status', '!=', 'Cancelado')
            ->where('branch_id', auth()->user()->branch_id)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Totales
        $totalSales = $salesData->sum();
        $totalPurchases = $purchasesData->sum();
        $totalRepairs = $repairsData->sum();

        // Normalizar datos para todos los meses
        $months = range(1, 12);
        $sales = [];
        $purchases = [];
        $repairs = [];
        foreach ($months as $month) {
            $sales[] = $salesData[$month] ?? 0;
            $purchases[] = $purchasesData[$month] ?? 0;
            $repairs[] = $repairsData[$month] ?? 0;
        }

        // Enviar datos a la vista
        return view('home', [
            'months' => ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            'sales' => $sales,
            'purchases' => $purchases,
            'repairs' => $repairs,
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'totalRepairs' => $totalRepairs,
        ]);
    }

    public function updateSucursal(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|exists:branches,id',
        ]);

        $user = User::find(auth()->user()->id);

        $user->branch_id = $request->sucursal_id;
        $user->save();

        return redirect()->back()->with('success-sucursal', 'Sucursal actualizada correctamente.');
    }
}
