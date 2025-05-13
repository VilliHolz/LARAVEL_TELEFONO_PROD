<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\Repair;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Crypt;

class RepairController extends Controller
{
    public function generateTicket($id_encriptado)
    {
        try {
            // Desencriptar el ID
            $repair_id = Crypt::decrypt($id_encriptado);

            // Obtener la venta y sus detalles
            $repair = Repair::with('brand', 'contact', 'user', 'branch')->findOrFail($repair_id);

            // Cargar el contenido del CSS de manera manual
            $css = file_get_contents(public_path('assets/admin/css/ticket.css'));

            // Datos a pasar a la vista
            $data = [
                'repair' => $repair,
                'css' => $css
            ];

            $pdfView = view('admin.tickets.repair', $data)->render();

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
