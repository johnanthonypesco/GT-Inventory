<?php

namespace App\Exports;

use App\Models\Inventory;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;

class InventoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $branch;
    protected $month;
    protected $year;
    protected $user;

    public function __construct($branch = null, $month = null, $year = null)
    {
        $this->branch = $branch;
        $this->month = $month ?: now()->month;
        $this->year = $year ?: now()->year;
        $this->user = Auth::user(); // get the logged-in user
    }

    public function collection()
    {
        $query = Inventory::with(['product', 'branch'])
            ->where('is_archived', 2);

        if ($this->branch) {
            $query->where('branch_id', $this->branch);
        }

        $items = $query->get()->sortBy('expiry_date');

        // Group by month and year
        $grouped = $items->groupBy(function ($item) {
            return Carbon::parse($item->expiry_date)->format('F Y');
        });

        $final = collect();

        foreach ($grouped as $monthLabel => $records) {

            // Insert month heading row
            $final->push((object)[
                'is_month_header' => true,
                'month_label' => $monthLabel,
            ]);

            // Insert actual inventory rows
            foreach ($records as $record) {
                $record->is_month_header = false;
                $final->push($record);
            }
        }

        return $final;
    }


    public function headings(): array
    {
        // First row: authorized personnel
        return [
            "RHU-" . $this->branch . ' Inventory Report Exported By: ' . ($this->user?->name ?? 'Unknown'),
            '', '', '', '', '', ''
        ];
    }

    public function map($item): array
    {
        if (!empty($item->is_month_header) && $item->is_month_header === true) {
            return [
                $item->month_label, '', '', '', ''
            ];
        }

        $generic_name = $item->product?->generic_name ?? 'No Generic Name';
        $brand_name = $item->product?->brand_name ?? 'No Brand Name';
        $expiry = Carbon::parse($item->expiry_date)->translatedFormat('M d, Y');

        return [
            $item->batch_number,
            $generic_name, 
            $brand_name,
            $item->product?->form ?? 'No Form',
            $item->product?->strength ?? 'No Strength',
            $item->quantity,
            $expiry,
        ];
    }


    public function styles(Worksheet $sheet)
    {
        // Merge the first row for personnel
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
        ]);

        // Header row (column titles)
        $sheet->fromArray(['Batch Number', 'Generic Name', 'Brand Name', 'Form', 'Strength','Quantity','Expiry Date'], null, 'A2');
        $sheet->getStyle('A2:G2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => 'solid',
                'color' => ['rgb' => '7F1D1D']
            ],
        ]);

        // Auto sizing
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Body font
        $sheet->getStyle('A3:G10000')->applyFromArray([
            'font' => ['size' => 12],
        ]);

        // ----------------------------
        // MONTH HEADER DETECTION HERE
        // ----------------------------
        $highestRow = $sheet->getHighestRow();

        for ($row = 3; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell("A{$row}")->getValue();

            // Detect month header (e.g., "January 2025")
            if (preg_match('/^[A-Za-z]+\s\d{4}$/', $cellValue)) {
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13],
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'FEE2E2'], // light red highlight
                    ],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                ]);
            }
        }

        return [];
    }
}
