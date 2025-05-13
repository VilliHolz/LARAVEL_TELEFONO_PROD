<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Sale;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Crypt;

class SaleController extends Controller
{
    public function generateTicket($id_encriptado)
    {
        try {
            // Desencriptar el ID
            $sale_id = Crypt::decrypt($id_encriptado);

            // Obtener la venta y sus detalles
            $sale = Sale::with('details.product', 'contact', 'branch')->findOrFail($sale_id);
            // Datos de la empresa
            $company = [
                'name' => $sale->branch->name,
                'address' => $sale->branch->address,
                'phone' => $sale->branch->phone,
                'footer' => $sale->branch->message,
            ];

            // Formatear los detalles para la vista
            $items = $sale->details->map(function ($detail) {
                return [
                    'name' => $detail->product->name,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                ];
            });

            // Cargar el contenido del CSS de manera manual
            $css = file_get_contents(public_path('assets/admin/css/ticket.css'));

            // Datos a pasar a la vista
            $data = [
                'company' => $company,
                'date' => $sale->date,
                'customer' => $sale->contact->name ?? 'Cliente Genérico',
                'items' => $items,
                'total' => $sale->total,
                'css' => $css // Pasar el CSS al PDF
            ];

            // Cargar la vista con el CSS incrustado
            $pdfView = view('admin.tickets.sale', $data)->render();

            // Configurar opciones de Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');

            // Inicializar Dompdf con las opciones
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($pdfView);

            // Establecer el tamaño de papel (80mm de ancho)
            $dompdf->setPaper([0, 0, 226.77, 600], 'portrait');

            // Renderizar el PDF
            $dompdf->render();

            // Devolver el PDF como una respuesta con tipo de contenido correcto
            return response($dompdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="ticket.pdf"'); // Para mostrarlo en el navegador
        } catch (\Exception $e) {
            // Manejo de errores
            return back()->with('error', 'No se pudo generar el ticket: ' . $e->getMessage());
        }
    }
}
