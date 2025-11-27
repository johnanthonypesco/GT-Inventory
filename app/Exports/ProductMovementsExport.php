<?php

namespace App\Exports;

use App\Models\ProductMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ProductMovementsExport implements 
    FromQuery, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithDrawings, 
    WithCustomStartCell, 
    WithEvents
{
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Letterhead');
        $drawing->setDescription('Official Header');
        
        $path = public_path('images/letterhead.png'); 
        if (!file_exists($path)) {
            $path = public_path('letterhead.png');
        }

        if (file_exists($path)) {
            $drawing->setPath($path);
            $drawing->setWidth(1485); 
            $drawing->setCoordinates('A1');
            $drawing->setOffsetY(5);
        } else {
            return [];
        }

        return $drawing;
    }

    // FIX 1: Push table down to Row 12 to make room for image + titles
    public function startCell(): string
    {
        return 'A19'; 
    }

    public function query()
    {
        $query = ProductMovement::with(['product', 'user', 'inventory.branch'])
            ->orderBy('created_at', $this->params['sort'] ?? 'desc');

        if (!empty($this->params['search'])) {
            $search = $this->params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('inventory', fn($q_inv) => $q_inv->where('batch_number', 'like', "%{$search}%"));
            });
        }

        if (!empty($this->params['product_id'])) $query->where('product_id', $this->params['product_id']);
        if (!empty($this->params['type'])) $query->where('type', $this->params['type']);
        if (!empty($this->params['user_id'])) $query->where('user_id', $this->params['user_id']);
        if (!empty($this->params['branch_id'])) {
            $query->whereHas('inventory', fn($q) => $q->where('branch_id', $this->params['branch_id']));
        }
        if (!empty($this->params['from'])) {
            $query->where('created_at', '>=', Carbon::parse($this->params['from'])->startOfDay());
        }
        if (!empty($this->params['to'])) {
            $query->where('created_at', '<=', Carbon::parse($this->params['to'])->endOfDay());
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Date & Time',
            'Branch',
            'Product Name',
            'Batch #',
            'Type',
            'Qty Change',
            'Before',
            'After',
            'Description',
            'User'
        ];
    }

    public function map($movement): array
    {
        $qty = $movement->quantity;
        if ($movement->type === 'OUT') {
            $qty = -1 * abs($qty);
        }

        return [
            $movement->created_at->format('M d, Y h:i A'),
            $movement->inventory->branch->name ?? 'N/A', 
            $movement->product->generic_name . ' (' . $movement->product->brand_name . ')',
            $movement->inventory->batch_number ?? 'N/A',
            $movement->type,
            $qty, 
            $movement->quantity_before ?? '0', // Corrected Column Name
            $movement->quantity_after ?? '0',  // Corrected Column Name
            $movement->description,
            $movement->user->name ?? 'System',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // FIX 2: Move Title to Row 9 (was 7) to avoid image overlap
                $sheet->mergeCells('A16:J16');
                $sheet->setCellValue('A16', 'Product Movement Report');
                $sheet->getStyle('A16')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // FIX 3: Move Metadata to Row 10 (was 8)
                $user = Auth::user()->name ?? 'Guest';
                $date = now()->format('M d, Y h:i A');
                $sheet->mergeCells('A17:J17');
                $sheet->setCellValue('A17', "Exported By: $user • Generated: $date");
                $sheet->getStyle('A17')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Conditional Formatting for Red Text
                // Loop starts at Row 13 because data starts there (Header is 12)
                $highestRow = $sheet->getHighestRow();
                for ($row = 20; $row <= $highestRow; $row++) {
                    $qtyCell = $sheet->getCell("F$row")->getValue();
                    if ($qtyCell < 0) {
                        $sheet->getStyle("F$row")->getFont()->setColor(new Color(Color::COLOR_RED));
                    } else {
                         $sheet->getStyle("F$row")->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                    }
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // FIX 4: Update Style Target to Row 12 (Where headers now live)
        $sheet->getStyle('A19:J19')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1F2937']], 
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (range('A', 'J') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}