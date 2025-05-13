<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\SaleDetail;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromArray, WithHeadings, WithStyles
{
    protected $empresa;

    public function __construct($empresa)
    {
        $this->empresa = $empresa;
    }

    /**
     * Devuelve los datos para el archivo Excel
     */
    public function array(): array
    {
        // Información de la empresa
        $companyInfo = [
            [$this->empresa->name],
            ['Dirección: ' . $this->empresa->address],
            ['Teléfono: ' . $this->empresa->phone],
            ['Correo Electrónico: ' . $this->empresa->email],
            [], // Espacio vacío antes de los encabezados
        ];

        // Encabezados principales
        $headers = [['Fecha', 'Total', 'Método de Pago', 'Método', 'Usuario', 'Correo Usuario', 'Cliente', 'Correo Cliente']];

        // Datos de ventas y detalles
        $data = [];
        $sales = Sale::with(['user', 'contact', 'paymentMethod', 'details.product']) // Relacionar usuario, contacto, método de pago y detalles con producto
            ->where('status', 'Activo') // Filtrar por ventas activas
            ->get();

        foreach ($sales as $sale) {
            // Agregar datos de la venta
            $data[] = [
                $sale->date,
                $sale->total,
                $sale->paymentMethod->name ?? 'Sin método',
                $sale->method, // Contado o Crédito
                $sale->user->name ?? 'Sin usuario',
                $sale->user->email ?? 'Sin correo',
                $sale->contact->name ?? 'Sin cliente',
                $sale->contact->email ?? 'Sin correo',
            ];

            // Encabezado de los productos (solo si hay productos en la venta)
            if ($sale->details->isNotEmpty()) {
                $data[] = ['Producto', 'Cantidad', 'Precio Unitario', 'Subtotal'];
                foreach ($sale->details as $detail) {
                    $data[] = [
                        $detail->product->name ?? 'Sin producto',
                        $detail->quantity,
                        $detail->price,
                        $detail->quantity * $detail->price, // Subtotal
                    ];
                }
            }

            $data[] = []; // Espacio vacío entre ventas
        }

        return array_merge($companyInfo, $headers, $data);
    }

    /**
     * Estilos del archivo Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para los datos de la empresa
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        $sheet->mergeCells('A4:H4');
        $sheet->getStyle('A1:A4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        // Estilo exclusivo para los encabezados de ventas
        $sheet->getStyle('A6:H6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '4CAF50'], // Verde
            ],
        ]);

        // Ajustar columnas
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Estilo para los encabezados de productos
        $sheet->getStyle('A7:H7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'CCCCCC'], // Gris claro
            ],
        ]);
    }

    /**
     * Define los encabezados de las columnas de datos (opcional, ya se incluyen en `array`)
     */
    public function headings(): array
    {
        return []; // Vacío porque ya incluimos los encabezados manualmente
    }
}