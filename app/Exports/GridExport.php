<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Datagrid;
use App\Services\GridRenderer;

class GridExport implements FromView, ShouldAutoSize
{
    public $dataGrid;
    public $renderer;

    public function __construct(Datagrid $dataGrid)
    {
        $this->dataGrid = $dataGrid;
        $this->renderer = new GridRenderer($dataGrid);
    }

    public function view(): View
    {
        return view('exports.grid', [
            'headers' => $this->renderer->getHeaders(),
            'body' => $this->renderer->getBody(true)
        ]);
    }
}
