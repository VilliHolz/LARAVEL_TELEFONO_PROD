<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactsExport implements FromArray, WithHeadings, WithStyles
{
    protected $empresa, $type;

    public function __construct($empresa, $type)
    {
        $this->empresa = $empresa;
        $this->type = $type;
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
        $headers = [['Nombre', 'Teléfono', 'Dirección', 'Correo Electrónico']];

        // Datos de contactos
        $contacts = Contact::where('type', $this->type)
            ->where('status', 'Activo')
            ->get(['name', 'phone', 'address', 'email'])
            ->toArray();

        return array_merge($companyInfo, $headers, $contacts);
    }

    /**
     * Estilos del archivo Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para los datos de la empresa
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');
        $sheet->mergeCells('A4:D4');
        $sheet->getStyle('A1:A4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        // Estilo exclusivo para los encabezados
        $sheet->getStyle('A5:D5')->applyFromArray([
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
        foreach (range('A', 'D') as $column) {
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
