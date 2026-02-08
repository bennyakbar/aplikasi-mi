<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Fee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TunggakanExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithColumnFormatting
{
    protected $data;

    public function __construct()
    {
        $this->data = $this->getTunggakanData();
    }

    protected function getTunggakanData()
    {
        return Student::where('status', 'active')
            ->with(['category', 'payments'])
            ->get()
            ->map(function ($student) {
                $totalFees = Fee::where('category_id', $student->category_id)
                    ->where('is_active', true)
                    ->sum('amount');

                $totalPaid = $student->payments->sum('total_amount');
                $tunggakan = max(0, $totalFees - $totalPaid);
                $percentage = $totalFees > 0 ? round(($totalPaid / $totalFees) * 100, 1) : 100;

                return [
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'category' => $student->category->name ?? '-',
                    'grade' => $student->grade,
                    'total_fees' => $totalFees,
                    'total_paid' => $totalPaid,
                    'tunggakan' => $tunggakan,
                    'percentage' => $percentage,
                ];
            })
            ->filter(fn($item) => $item['tunggakan'] > 0)
            ->sortByDesc('tunggakan')
            ->values();
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama Siswa',
            'Kategori',
            'Kelas',
            'Total Biaya',
            'Total Dibayar',
            'Tunggakan',
            '% Terbayar',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Add borders to all data
        $lastRow = $this->data->count() + 1;
        $sheet->getStyle("A1:H{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Highlight high tunggakan (> 50% unpaid)
        foreach ($this->data as $index => $row) {
            $rowNum = $index + 2;
            if ($row['percentage'] < 50) {
                $sheet->getStyle("A{$rowNum}:H{$rowNum}")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEE2E2'],
                    ],
                ]);
            }
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 30,
            'C' => 18,
            'D' => 10,
            'E' => 18,
            'F' => 18,
            'G' => 18,
            'H' => 12,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public function title(): string
    {
        return 'Laporan Tunggakan';
    }
}
