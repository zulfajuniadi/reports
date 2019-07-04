<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Report;
use App\Services\GridRenderer;
use App\Models\Datagrid;
use App\Exports\GridExport;
use \Excel;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {
        $menus = Menu::whereType('report')
            ->whereNotIn('href', ['#', ''])
            ->whereNotNull('href')
            ->orderBy('parent_id')
            ->orderBy('sort_index')
            ->get();
        $report = null;
        foreach ($menus as $menu) {
            if ($report!=null) {
                continue;
            }
            $report = Report::whereSlug($menu->href)->first();
        }
        if (!$report) {
            return redirect()->route('setup');
        }

        return redirect()->route('report', $report->slug);
    }

    public function report($slug)
    {
        $report = Report::whereSlug($slug)->first();
        if (!$report) {
            return 'Link broken';
        }
        $menus = [];
        $this->expandMenu(Menu::whereNull('parent_id')->orderBy('sort_index')->get(), $menus);
        $report->data = json_decode($report->data);
        $renderer = new GridRenderer(new Datagrid());
        $gridTitle = '';
        $exportUrl = '';
        if (in_array($report->type, [
            'Grid only',
            'Grid with charts on the left',
            'Grid with charts on the right'
        ]) && $report->data && $report->data->grid && $report->data->grid->id && $grid = Datagrid::find($report->data->grid->id)) {
            $renderer = new GridRenderer($grid);
            $gridTitle = $grid->name;
        }
        return view('report', compact('report', 'menus', 'renderer', 'gridTitle', 'exportUrl'));
    }

    public function getHeaders($slug)
    {
        $report = Report::whereSlug($slug)->firstOrFail();
        $report->data = json_decode($report->data);
        if ($report->data && $report->data->grid && $report->data->grid->id && $grid = Datagrid::find($report->data->grid->id)) {
            $renderer = new GridRenderer($grid);
            return $renderer->getHeaders();
        }
        abort(404);
    }

    public function getBody($slug)
    {
        $report = Report::whereSlug($slug)->firstOrFail();
        $report->data = json_decode($report->data);
        if ($report->data && $report->data->grid && $report->data->grid->id && $grid = Datagrid::find($report->data->grid->id)) {
            $renderer = new GridRenderer($grid);
            return $renderer->getBody();
        }
        abort(404);
    }

    public function getFilters($slug)
    {
        $report = Report::whereSlug($slug)->firstOrFail();
        $report->data = json_decode($report->data);
        if ($report->data && $report->data->grid && $report->data->grid->id && $grid = Datagrid::find($report->data->grid->id)) {
            $renderer = new GridRenderer($grid);
            return $renderer->getFilters();
        }
        abort(404);
    }

    protected function expandMenu($menus = [], &$result)
    {
        foreach ($menus as $menu) {
            $menuData = $menu->toArray();
            $nodes = [];
            $this->expandMenu($menu->children, $nodes);
            $menuData['nodes'] = $nodes;
            $result[] = $menuData;
        }
    }

    public function export($slug)
    {
        $report = Report::whereSlug($slug)->firstOrFail();
        $report->data = json_decode($report->data);
        if ($report->data && $report->data->grid && $report->data->grid->id && $grid = Datagrid::find($report->data->grid->id)) {
            libxml_use_internal_errors(true);
            return Excel::download(new GridExport($grid), Str::slug($report->name) . '_' . date('YmdHis') . '.xlsx');
        }
        abort(404);
    }

    public function preview($slug)
    {
        $report = Report::whereSlug($slug)->firstOrFail();
        $report->data = json_decode($report->data);
        if ($report->data && $report->data->grid && $report->data->grid->id && $grid = Datagrid::find($report->data->grid->id)) {
            $renderer = new GridRenderer($grid);
            return view('exports.grid', [
                'body' => $renderer->getBody(true)
            ]);
        }
    }
}
