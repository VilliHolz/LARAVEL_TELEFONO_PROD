<?php

namespace App\Exports;

use App\Models\CashRegister;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashRegistersExport implements FromArray, WithHeadings, WithStyles
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
        $headers = [['Fecha de Inicio', 'Hora de Inicio', 'Fecha de Cierre', 'Monto Inicial', 'Monto Final', 'Estado', 'Usuario', 'Correo Usuario']];

        // Datos de las cajas
        $data = [];
        $cashRegisters = CashRegister::with(['user']) // Relacionar solo el usuario
            ->where('status', 'Activo') // Filtrar por cajas activas
            ->get();

        foreach ($cashRegisters as $cashRegister) {
            // Agregar datos de la caja
            $data[] = [
                $cashRegister->start_date,
                $cashRegister->start_time,
                $cashRegister->end_date ?? 'Sin cierre',
                $cashRegister->initial_amount,
                $cashRegister->final_amount,
                $cashRegister->status,
                $cashRegister->user->name ?? 'Sin usuario',
                $cashRegister->user->email ?? 'Sin correo',
            ];
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

        // Estilo exclusivo para los encabezados de cajas
        $sheet->getStyle('A5:H5')->applyFromArray([
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
    }

    /**
     * Define los encabezados de las columnas de datos (opcional, ya se incluyen en `array`)
     */
    public function headings(): array
    {
        return []; // Vacío porque ya incluimos los encabezados manualmente
    }
}