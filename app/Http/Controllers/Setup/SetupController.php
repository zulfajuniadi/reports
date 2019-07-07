<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Menu;
use App\Models\Datagrid;
use Doctrine\DBAL\Driver\IBMDB2\DB2Driver;
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
        $models = app(Menu::class)->all()->where('parent_id', null)->sortBy('sort_index');
        $this->expandMenu($models, $menu);
        return $menu;
    }

    public function saveMenus(Request $request)
    {
        $menu = [];
        $this->flattenMenu($request->get('menus'), $menu);
        app(Menu::class)->all()->each(function ($model) {
            $model->delete();
        });
        foreach ($menu as $row) {
            app(Menu::class)->create($row);
        }
    }

    // Data Grids

    public function dataGrids()
    {
        return app(Datagrid::class)->orderBy('name')->get();
    }

    public function createDataGrid(Request $request)
    {
        $data = $request->except('_token');
        $data['fields'] = [];
        $data['is_enabled'] = '1';
        return app(Datagrid::class)->create($data);
    }

    public function showDataGrid($id)
    {
        $grid = app(Datagrid::class)->findOrFail($id);
        return $grid;
    }

    public function updateDataGrid(Request $request, $id)
    {
        $data = $request->except('id', '_token', 'created_at', 'updated_at');
        $grid = app(Datagrid::class)->findOrFail($id);
        $grid->update($data);
        return $grid;
    }

    public function deleteDataGrid($id)
    {
        $grid = app(Datagrid::class)->findOrFail($id);
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
        $grid = app(Datagrid::class)->findOrFail($id);
        $fields = [];
        foreach ($grid->fields as $field) {
            $fields[$field['sys_name']] = $field;
        }
        set_time_limit(600);
        $row = DB::connection(config('database.report_db'))->table($grid->view_name)->take(1)->get();
        if ($row->count() > 0) {
            $vars = get_object_vars($row[0]);
            foreach (array_keys($vars) as $index => $name) {
                $fields[$name] = [
                    'sys_name' => $name,
                    'sort_order' => isset($fields[$name]['sort_order']) ? $fields[$name]['sort_order'] : $index,
                    'name' => isset($fields[$name]['name']) ? $fields[$name]['name'] : Str::title(str_replace('_', ' ', $name)),
                    'filter_name' => isset($fields[$name]['filter_name']) ? $fields[$name]['filter_name'] : Str::title(str_replace('_', ' ', $name)),
                    'is_shown' =>  isset($fields[$name]['is_shown']) ? $fields[$name]['is_shown'] : '1',
                    'is_sortable' =>  isset($fields[$name]['is_sortable']) ? $fields[$name]['is_sortable'] : '1',
                    'has_filter' =>  isset($fields[$name]['has_filter']) ? $fields[$name]['has_filter'] : '0',
                    'has_default_filter' =>  isset($fields[$name]['has_default_filter']) ? $fields[$name]['has_default_filter'] : '0',
                    'filter_type' => isset($fields[$name]['filter_type']) ? $fields[$name]['filter_type'] : 'Search',
                    'data_type' => isset($fields[$name]['data_type']) ? $fields[$name]['data_type'] : guessDataType($vars[$name]),
                ];
            }
            $grid->fields = array_values($fields);
            $grid->save();
            return $grid;
        } else {
            return [];
        }
    }

    // Charts

    public function charts()
    {
        return app(Chart::class)->orderBy('name')->get();
    }
    
    public function showCharts($id)
    {
        return app(Chart::class)->findOrFail($id);
    }
    
    public function createCharts(Request $request)
    {
        $data = $request->except('_token');
        $data['type'] = 'Bar Chart';
        $data['labels'] = '// return $row->label;';
        $data['datasets'] = '// return $row->count;';
        $data['query'] = "\$query->where('year', date('Y'))->where('month', date('m'));\n";
        $data['bg_color'] = '#f9f9f9';
        $data['color_scheme'] = 'brewer.RdYlGn11';
        return app(Chart::class)->create($data);
    }
    
    public function updateCharts(Request $request, $id)
    {
        $chart = app(Chart::class)->findOrFail($id);
        $data = $request->except('_token');
        $chart->update($data);
        return $chart;
    }
    
    public function deleteCharts($id)
    {
        $chart = app(Chart::class)->findOrFail($id);
        $chart->delete();
    }

    public function getChartData($id)
    {
        $chart = app(Chart::class)->findOrFail($id);
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
        return app(Report::class)->orderBy('name')->get();
    }

    public function createReport(Request $request)
    {
        $suffix = '';
        
        if ($existingCount = app(Report::class)->where('name', $request->get('name'))->count()) {
            $suffix = '-' . $existingCount;
        }

        return app(Report::class)->create([
            'name' => $request->get('name'),
            'type' => $request->get('type'),
            'description' => $request->get('description'),
            'slug' => Str::slug($request->get('name')) . $suffix,
            'data' => ['rows' => [], 'grid' => ['id' => null]]
        ]);
    }

    public function report($id)
    {
        $report = app(Report::class)->findOrFail($id);
        return $report;
    }

    public function updateReport(Request $request, $id)
    {
        $report = app(Report::class)->findOrFail($id);
        $data = $request->except('slug', 'created_at', 'updated_at', 'token');
        if ($report->name != $data['name']) {
            $suffix = '';
            
            if ($existingCount = app(Report::class)->whereName($request->get('name'))->count()) {
                $suffix = '-' . $existingCount;
            }
            $data['slug'] = Str::slug($request->get('name')) . $suffix;
        }
        $report->update($data);
        return $report;
    }

    public function deleteReport($id)
    {
        app(Report::class)->findOrFail($id)->delete();
    }

    // utilities functions

    protected function expandMenu($menus = [], &$result)
    {
        foreach ($menus as $menu) {
            $menuData = $menu;
            $nodes = [];
            $this->expandMenu($menu->children(), $nodes);
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
