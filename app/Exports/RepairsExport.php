<?php

namespace App\Exports;

use App\Models\Repair;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RepairsExport implements FromArray, WithHeadings, WithStyles
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
        $headers = [['Fecha de Entrada', 'Fecha Prometida', 'Modelo', 'IMEI', 'Adelanto', 'Total', 'Estado', 'Usuario', 'Correo Usuario', 'Cliente', 'Marca', 'Productos']];

        // Datos de reparaciones y detalles
        $data = [];
        $repairs = Repair::with(['user', 'contact', 'brand', 'details.product']) // Relacionar usuario, contacto (cliente), marca y productos
            ->whereNot('status', 'Cancelado') // Excluir las reparaciones canceladas
            ->get();


        foreach ($repairs as $repair) {
            // Agregar datos de la reparación
            $data[] = [
                $repair->entry_date,
                $repair->promised_date,
                $repair->model,
                $repair->imei ?? 'Sin IMEI',
                $repair->advance,
                $repair->total,
                $repair->status,
                $repair->user->name ?? 'Sin usuario',
                $repair->user->email ?? 'Sin correo',
                $repair->contact->name ?? 'Sin cliente',
                $repair->brand->name ?? 'Sin marca',
                $repair->details->isNotEmpty() ? 'Sí' : 'No',
            ];

            // Si existen productos asociados a la reparación
            if ($repair->details->isNotEmpty()) {
                // Encabezado de los productos
                $data[] = ['Repuesto', 'Cantidad', 'Precio Unitario', 'Subtotal'];
                foreach ($repair->details as $detail) {
                    $product = $detail->product; // Obtener el producto asociado
                    $data[] = [
                        $product->name ?? 'Sin nombre', // Nombre del producto
                        $detail->quantity, // Cantidad
                        $detail->price, // Precio unitario
                        $detail->quantity * $detail->price, // Subtotal
                    ];
                }
            }

            $data[] = []; // Espacio vacío entre reparaciones
        }

        return array_merge($companyInfo, $headers, $data);
    }

    /**
     * Estilos del archivo Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para los datos de la empresa
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');
        $sheet->mergeCells('A4:L4');
        $sheet->getStyle('A1:A4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        // Estilo exclusivo para los encabezados de reparaciones
        $sheet->getStyle('A5:L5')->applyFromArray([
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
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Estilo para los encabezados de productos
        $sheet->getStyle('A6:L6')->applyFromArray([
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
