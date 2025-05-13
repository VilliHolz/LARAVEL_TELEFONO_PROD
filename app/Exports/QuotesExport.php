<?php

namespace App\Exports;

use App\Models\Quote;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuotesExport implements FromArray, WithHeadings, WithStyles
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

        // Encabezados
        $headers = [['Fecha', 'Total', 'Usuario', 'Correo Usuario', 'Contacto', 'Correo Contacto']];

        // Datos de cotizaciones y detalles
        $data = [];
        $quotes = Quote::with(['user', 'contact', 'details.product']) // Relacionar usuario, contacto y detalles con producto
            ->get();

        foreach ($quotes as $quote) {
            // Agregar cotización
            $data[] = [
                $quote->date,
                $quote->total,
                $quote->user->name ?? 'Sin usuario',
                $quote->user->email ?? 'Sin correo',
                $quote->contact->name ?? 'Sin contacto',
                $quote->contact->email ?? 'Sin correo',
            ];

            // Encabezado de los productos (solo si hay productos)
            if ($quote->details->isNotEmpty()) {
                $data[] = ['Producto', 'Cantidad', 'Precio Unitario', 'Subtotal']; // Encabezado para los productos
                foreach ($quote->details as $detail) {
                    $data[] = [
                        $detail->product->name ?? 'Sin producto',
                        $detail->quantity,
                        $detail->price,
                        $detail->quantity * $detail->price, // Subtotal
                    ];
                }
            }

            $data[] = []; // Espacio vacío entre cotizaciones
        }

        return array_merge($companyInfo, $headers, $data);
    }

    /**
     * Estilos del archivo Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para los datos de la empresa
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');
        $sheet->mergeCells('A4:F4');
        $sheet->getStyle('A1:A4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        // Estilo exclusivo para los encabezados de cotizaciones
        $sheet->getStyle('A5:F5')->applyFromArray([
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
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Estilo para los encabezados de productos
        $sheet->getStyle('A6:F6')->applyFromArray([
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