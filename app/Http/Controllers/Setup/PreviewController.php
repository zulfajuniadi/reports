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
        $this->grid = Datagrid::findOrFail(request()->segment(4));
        $this->renderer = new GridRenderer($this->grid);
    }

    public function showGrid()
    {
        return view('setup.preview.grid', [
            'grid' => $this->grid,
            'renderer' => $this->renderer,
        ]);
    }

    public function getFilters()
    {
        return $this->renderer->getFilters();
    }

    public function getBody()
    {
        return $this->renderer->getBody();
    }

    public function getDump()
    {
        return view('setup.preview.dump', [
            'title' => $this->grid->title,
            'grid' => $this->grid,
            'renderer' => $this->renderer,
        ]);
    }
}
