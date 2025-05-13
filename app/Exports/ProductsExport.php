<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromArray, WithHeadings, WithStyles
{
    protected $empresa;

    public function __construct($empresa) {
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
        $headers = [['Código de Barras', 'IMEI', 'Nombre', 'Precio Compra', 'Precio Venta', 'Stock', 'Stock Mínimo', 'Categoría', 'Marca', 'Estado']];

        // Datos de productos
        $products = Product::with(['brand', 'category'])
            ->where('status', 'Activo')
            ->get()
            ->map(function ($product) {
                return [
                    $product->barcode,
                    $product->imei,
                    $product->name,
                    $product->purchase_price,
                    $product->sale_price,
                    $product->stock,
                    $product->min_stock,
                    $product->category->name ?? 'Sin categoría',
                    $product->brand->name ?? 'Sin marca',
                    $product->status,
                ];
            })->toArray();

        return array_merge($companyInfo, $headers, $products);
    }

    /**
     * Estilos del archivo Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para los datos de la empresa
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        $sheet->mergeCells('A4:J4');
        $sheet->getStyle('A1:A4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        // Estilo exclusivo para los encabezados
        $sheet->getStyle('A6:J6')->applyFromArray([
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
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    /**
     * Define los encabezados de las columnas de datos (opcional, ya se incluyen en `array`)
     */
    public function headings(): array
    {
        return []; // Vacío porque ya incluimos los encabezados manualmente
    }
}