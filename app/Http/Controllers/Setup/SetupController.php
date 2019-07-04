<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Menu;
use App\Models\Datagrid;
use Doctrine\DBAL\Driver\IBMDB2\DB2Driver;
use App\Models\Field;
use App\Models\Chart;
use App\Models\Report;

class SetupController extends Controller
{
    public function index(Request $request)
    {
        return view('setup.index');
    }

    // Menus

    public function menus()
    {
        $menu = [];
        $this->expandMenu(Menu::whereNull('parent_id')->orderBy('sort_index')->get(), $menu);
        return $menu;
    }

    public function saveMenus(Request $request)
    {
        $menu = [];
        $this->flattenMenu($request->get('menus'), $menu);
        Menu::whereNotNull('id')->delete();
        foreach ($menu as $row) {
            Menu::create($row);
        }
    }

    // Data Grids

    public function dataGrids()
    {
        return Datagrid::orderBy('name')->get();
    }

    public function createDataGrid(Request $request)
    {
        $data = $request->except('_token');
        return Datagrid::create($data);
    }

    public function showDataGrid($id)
    {
        $grid =  Datagrid::find($id);
        $grid->fields = $grid->fields()->orderBy('sort_order')->get();
        return $grid;
    }

    public function updateDataGrid(Request $request, $id)
    {
        $data = $request->except('id', '_token', 'created_at', 'updated_at', 'fields');
        $grid = Datagrid::findOrFail($id);
        $grid->update($data);
        $fields = $request->get('fields');
        if ($fields) {
            foreach ($fields as $field) {
                Field::findOrFail($field['id'])->update($field);
            }
        }
        return $grid;
    }

    public function deleteDataGrid($id)
    {
        $grid = Datagrid::findOrFail($id);
        $grid->delete();
    }

    public function getViews()
    {
        $doctrine = DB::connection(config('database.report_db'))->getDoctrineConnection();
        $schemaManager = $doctrine->getSchemaManager();
        $views = array_keys($schemaManager->listViews());
        array_sort($views);
        return $views;
    }

    public function refreshFields($id)
    {
        $grid = Datagrid::findOrFail($id);
        set_time_limit(600);
        $row = DB::connection(config('database.report_db'))->table($grid->view_name)->take(1)->get();
        if ($row->count() > 0) {
            $vars = get_object_vars($row[0]);
            $found = [];
            foreach (array_keys($vars) as $index => $name) {
                $found[] = $name;
                $existing = $grid->fields()->whereSysName($name)->first();
                $data = [];
                if ($existing) {
                    $data = $existing->toArray();
                }
                $grid->fields()->updateOrCreate([
                    'sys_name' => $name,
                    'datagrid_id' => $grid->id
                ], [
                    'sort_order' => isset($data['sort_order']) ? $data['sort_order'] : $index,
                    'name' => isset($data['name']) ? $data['name'] : Str::title(str_replace('_', ' ', $name)),
                    'filter_name' => isset($data['filter_name']) ? $data['filter_name'] : Str::title(str_replace('_', ' ', $name)),
                    'is_shown' =>  isset($data['is_shown']) ? $data['is_shown'] : true,
                    'has_filter' =>  isset($data['has_filter']) ? $data['has_filter'] : false,
                    'filter_type' => isset($data['filter_type']) ? $data['filter_type'] : 'Search',
                    'data_type' => isset($data['data_type']) ? $data['data_type'] : guessDataType($vars[$name]),
                ]);
            }
            $grid->fields()->whereNotIn('sys_name', $found)->delete();
            $grid->fields = $grid->fields()->orderBy('is_shown', 'desc')->orderBy('sort_order')->get();
            return $grid;
        } else {
            return [];
        }
    }

    // Charts

    public function charts()
    {
        return Chart::orderBy('name')->get();
    }
    
    public function showCharts($id)
    {
        $chart = Chart::findOrFail($id);
        return $chart;
    }
    
    public function createCharts(Request $request)
    {
        $data = $request->except('_token');
        $data['type'] = 'Bar Chart';
        $data['labels'] = '// return $row->label;';
        $data['datasets'] = '// return $row->count;';
        $data['query'] = "\$query->where('year', date('Y'))->where('month', date('m'));\n";
        return Chart::create($data);
    }
    
    public function updateCharts(Request $request, $id)
    {
        $chart = Chart::findOrFail($id);
        $data = $request->except('_token');
        $chart->update($data);
        return $chart;
    }
    
    public function deleteCharts($id)
    {
        $chart = Chart::findOrFail($id);
        $chart->delete();
    }

    public function getChartData($id)
    {
        $chart = Chart::findOrFail($id);
        $query = DB::connection(config('database.report_db'))->table($chart->view_name);
        if ($chart->query) {
            eval($chart->query) . ';';
        }
        $results = $query->get();
        return [
            'labels' => $results->map(function ($row) use ($chart) {
                return eval($chart->labels . ';');
            }),
            'datasets' => $results->map(function ($row) use ($chart) {
                return eval($chart->datasets . ';');
            })
        ];
    }

    // Reports

    public function reports()
    {
        return Report::orderBy('name')->get();
    }

    public function createReport(Request $request)
    {
        $suffix = '';
        
        if ($existingCount = Report::whereName($request->get('name'))->count()) {
            $suffix = '-' . $existingCount;
        }

        return Report::create([
            'name' => $request->get('name'),
            'type' => $request->get('type'),
            'description' => $request->get('description'),
            'slug' => Str::slug($request->get('name')) . $suffix,
            'data' => '{"rows":[],"grid":{"id":null}}'
        ]);
    }

    public function report($id)
    {
        $report = Report::findOrFail($id);
        $report->data = json_decode($report->data);
        return $report;
    }

    public function updateReport(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $data = $request->except('slug', 'created_at', 'updated_at', 'token');
        if ($report->name != $data['name']) {
            $suffix = '';
            
            if ($existingCount = Report::whereName($request->get('name'))->count()) {
                $suffix = '-' . $existingCount;
            }
            $data['slug'] = Str::slug($request->get('name')) . $suffix;
        }
        $data['data'] = json_encode($data['data']);
        $report->update($data);
        return $report;
    }

    public function deleteReport($id)
    {
        Report::findOrFail($id)->delete();
    }

    // utilities functions

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

    protected function flattenMenu($menus = [], &$result, $parent = null)
    {
        foreach ($menus as $index => $menu) {
            $result[] = [
                'id' => $menu['id'],
                'parent_id' => isset($parent) ? $parent['id'] : null,
                'sort_index' => $index,
                'is_enabled' => $menu['is_enabled'],
                'title' => $menu['title'],
                'type' => $menu['type'],
                'href' => $menu['href'] ?? '' ,
            ];
            $this->flattenMenu($menu['nodes'], $result, $menu);
        }
    }
}
