<?php

namespace App\Exports;

use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;       // Needed for Images
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // Needed to push data down
use Maatwebsite\Excel\Concerns\WithEvents;         // Needed for custom titles/metadata
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InventoryExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithDrawings, 
    WithCustomStartCell, 
    WithEvents
{
    protected $branch;
    protected $filter;
    protected $search;
    protected $user;

    public function __construct($branch, $filter = null, $search = null)
    {
        $this->branch = $branch;
        $this->filter = $filter;
        $this->search = $search;
        $this->user = Auth::user();
    }

    /**
     * 1. Add the Letterhead Image
     */
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Letterhead');
        $drawing->setDescription('Official Header');
        
        // Ensure the file exists to prevent errors
        $path = public_path('/images/letterhead.png');
        if (file_exists($path)) {
            $drawing->setPath($path);
        } else {
            // Fallback or skip if image missing
            return [];
        }

        $drawing->setWidth(720); // Adjust height based on your image aspect ratio
        $drawing->setCoordinates('A1');
        $drawing->setOffsetY(5); // Small top padding

        return $drawing;
    }

    /**
     * 2. Push the Data Table down to Row 10
     * Rows 1-6: Image
     * Row 7: Report Title
     * Row 8: Metadata (Exported by...)
     * Row 9: Empty/Spacing
     * Row 10: Column Headers
     */
    public function startCell(): string
    {
        return 'A10';
    }

    /**
     * 3. Fetch and Group Data
     */
    public function collection()
    {
        $query = Inventory::with(['product', 'branch'])
            ->where('branch_id', $this->branch)
            ->where('is_archived', 0);

        // Apply Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('product', function ($pq) {
                    $pq->where('generic_name', 'like', "%{$this->search}%")
                       ->orWhere('brand_name', 'like', "%{$this->search}%");
                })->orWhere('batch_number', 'like', "%{$this->search}%");
            });
        }

        // Apply Filter (Using match for cleaner syntax)
        if ($this->filter) {
            match ($this->filter) {
                'in_stock' => $query->where('quantity', '>=', 100),
                'low_stock' => $query->where('quantity', '>', 0)->where('quantity', '<', 100),
                'out_of_stock' => $query->where('quantity', '<=', 0),
                'nearly_expired' => $query->whereBetween('expiry_date', [now(), now()->addDays(30)]),
                'expired' => $query->where('expiry_date', '<', now()),
                default => null,
            };
        }

        $items = $query->get()->sortBy('expiry_date');

        // Logic to insert "Month Headers" into the collection
        $final = collect();
        $grouped = $items->groupBy(fn($item) => Carbon::parse($item->expiry_date)->format('F Y'));

        foreach ($grouped as $monthLabel => $records) {
            // Push Header Object
            $final->push((object)[
                'is_month_header' => true,
                'month_label' => $monthLabel,
            ]);

            // Push Records
            foreach ($records as $record) {
                $record->is_month_header = false;
                $final->push($record);
            }
        }

        return $final->isEmpty() ? collect([(object)['empty' => true]]) : $final;
    }

    /**
     * 4. Column Headers (Starts at A10)
     */
    public function headings(): array
    {
        return [
            'Batch Number', 
            'Generic Name', 
            'Brand Name', 
            'Form', 
            'Strength', 
            'Quantity', 
            'Expiry Date'
        ];
    }

    /**
     * 5. Map Data to Columns
     */
    public function map($item): array
    {
        if (!empty($item->empty)) {
            return ['No records found with the current filter.', '', '', '', '', '', ''];
        }

        if (!empty($item->is_month_header)) {
            // Only fill first column, we will merge in styles()
            return [$item->month_label, '', '', '', '', '', ''];
        }

        return [
            $item->batch_number,
            $item->product?->generic_name ?? '—',
            $item->product?->brand_name ?? '—',
            $item->product?->form ?? '—',
            $item->product?->strength ?? '—',
            $item->quantity, // Keep as integer for Excel math
            Carbon::parse($item->expiry_date)->translatedFormat('M d, Y'),
        ];
    }

    /**
     * 6. Custom Title and Metadata via Events
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // --- Report Title (Row 7) ---
                $sheet->mergeCells('A7:G7');
                $sheet->setCellValue('A7', "RHU-{$this->branch} Inventory Report");
                $sheet->getStyle('A7')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // --- Filter & User Info (Row 8) ---
                $by = $this->user?->name ?? 'Unknown';
                $date = now()->format('M d, Y h:i A');
                
                $filterText = 'All Items';
                if ($this->filter) {
                    $labels = [
                        'in_stock' => 'In Stock (≥100)',
                        'low_stock' => 'Low Stock (1–99)',
                        'out_of_stock' => 'Out of Stock',
                        'nearly_expired' => 'Nearly Expired (<30 days)',
                        'expired' => 'Expired',
                    ];
                    $filterText = $labels[$this->filter] ?? ucfirst($this->filter);
                }
                if ($this->search) $filterText .= " | Search: \"{$this->search}\"";

                $sheet->mergeCells('A8:G8');
                $sheet->setCellValue('A8', "Exported By: {$by} • Date: {$date} • Filter: {$filterText}");
                $sheet->getStyle('A8')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '4B5563']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
            },
        ];
    }

    /**
     * 7. Styling the Table
     */
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Style the Main Column Headers (Row 10)
        $sheet->getStyle('A10:G10')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '7F1D1D']], // Red Header
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Auto Size Columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Loop through rows to style Data and Month Headers
        for ($row = 11; $row <= $highestRow; $row++) {
            $val = $sheet->getCell("A{$row}")->getValue();

            // Check if it is a Month Header (Regex: "Monthname YYYY")
            if (preg_match('/^[A-Za-z]+\s+\d{4}$/', $val) && $sheet->getCell("B{$row}")->getValue() == '') {
                
                // Style Month Group Header
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']], // Light Gray
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

            } else {
                // Style Normal Data Rows
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]]
                ]);
                
                // Center Align Quantity (Col F) and Expiry (Col G)
                $sheet->getStyle("F{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        return [];
    }
}