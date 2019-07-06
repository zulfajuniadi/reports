<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Datagrid;
use App\Services\GridRenderer;
use App\Services\Table;

class PreviewController extends Controller
{
    protected $renderer;
    protected $grid;

    public function __construct()
    {
        $this->grid = app(Datagrid::class)->findOrFail(intval(request()->segment(4)));
        $this->renderer = new GridRenderer($this->grid);
    }

    public function showGrid()
    {
        return view('setup.preview', [
            'grid' => $this->grid,
            'renderer' => $this->renderer,
        ]);
    }

    public function getFilters()
    {
        return $this->renderer->getFilters();
    }

    public function getHeaders()
    {
        return $this->renderer->getHeaders();
    }

    public function getBody()
    {
        return $this->renderer->getBody();
    }
}
