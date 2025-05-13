<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditSale;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Crypt;

class SaleCreditController extends Controller
{

    public function generateTicket($id_encriptado)
    {
        try {
            // Desencriptar el ID
            $credit_id = Crypt::decrypt($id_encriptado);

            // Obtener la venta y sus detalles, asegurando relaciones necesarias
            $creditSale = CreditSale::with(['payments.user', 'sale.branch'])->findOrFail($credit_id);

            // Datos de la empresa
            $company = [
                'name' => $creditSale->sale->branch->name,
                'address' => $creditSale->sale->branch->address,
                'phone' => $creditSale->sale->branch->phone,
                'footer' => $creditSale->sale->branch->message,
            ];

            // Cargar el contenido del CSS de manera manual
            $css = file_get_contents(public_path('assets/admin/css/ticket.css'));

            // Construir los items desde los pagos
            $items = $creditSale->payments->map(function ($payment) {
                return [
                    'name' => 'Abono de Crédito', // Cambia según sea necesario
                    'quantity' => 1,
                    'price' => $payment->amount,
                    'user' => $payment->user->name ?? 'Usuario desconocido',
                    'date' => $payment->date,
                ];
            });

            // Datos a pasar a la vista
            $data = [
                'company' => $company,
                'date' => $creditSale->created_at->format('d/m/Y H:i'),
                'customer' => $creditSale->sale->contact->name ?? 'Cliente Genérico',
                'items' => $items,
                'total' => $creditSale->amount,
                'css' => $css, // Pasar el CSS al PDF
            ];

            // Cargar la vista con el CSS incrustado
            $pdfView = view('admin.tickets.credit', $data)->render();

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
