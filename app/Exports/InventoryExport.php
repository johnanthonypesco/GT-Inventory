<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InventoryExport implements FromView
{
    protected $inventory;
    protected $type;
    protected $subType;

    public function __construct($inventory, $type, $subType)
    {
        $this->inventory = $inventory;
        $this->type = $type;
        $this->subType = $subType;
    }

    public function view(): View
    {
        return view('exports.inventory', [
            'inventory' => $this->inventory,
            'type' => $this->type,
            'subType' => $this->subType,
        ]);
    }
}
