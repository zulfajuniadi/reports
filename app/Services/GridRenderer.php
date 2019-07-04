<?php

namespace App\Services;

use App\Models\Datagrid;
use App\Services\Table;
use App\Models\Field;

class GridRenderer
{
    protected $grid;
    protected $params;

    public function __construct(Datagrid $grid)
    {
        $this->grid = $grid;
        $this->params = request()->all();
    }
    
    public function renderFilters()
    {
        return view('grid.partials.filters', ['grid' => $this->grid]);
    }

    public function renderBody()
    {
        return view('grid.partials.body-ajax', ['grid' => $this->grid]);
    }

    public function renderScripts()
    {
        return view('grid.partials.scripts', ['grid' => $this->grid]);
    }

    public function render($title = '', $render_header = true)
    {
        $viewData = [
            'render_header' => $render_header,
            'title' => $title ? $title : $this->grid->name,
            'renderer' => $this,
            'grid' => $this->grid,
            'default_sort' => null,
            'default_sort_direction' => 'asc',
        ];
        return view('grid.grid', $viewData);
    }

    public function getBody($skipLinks = false)
    {
        return (new Table($this->grid))->generate($skipLinks);
    }

    public function dump()
    {
        return view('grid.dump', [
            'title' => $this->grid->name,
            'grid' => $this->grid,
            'renderer' => $this
        ]);
    }

    public function getFilters()
    {
        $filters = [];
        $plucks = [];
        foreach ($this->grid->fields()->orderBy('sort_order')->get() as $field) {
            if ($field->is_shown && $field->has_filter) {
                $values = [];
                if (in_array($field->filter_type, ['Drop Down', 'Multiple Drop Down'])) {
                    $plucks[] = $field->sys_name;
                }
                $filter = [
                    'name' => $field->filter_name,
                    'field' => $field->sys_name,
                    'type' => $field->filter_type,
                    'values' => $values
                ];
                if ($field->default_filter_value) {
                    $filter['value'] = eval($field->default_filter_value);
                }
                $filters[$field->sys_name] = $filter;
            }
        }
        if (count($plucks) > 0) {
            $query = \DB::connection(config('database.report_db'))
                ->table($this->grid->view_name)
                ->select($plucks)
                ->groupBy($plucks);

            if ($range = request()->get('created_at')) {
                $query->whereBetween('created_at', $range);
            }

            $result = $query->get();

            foreach ($plucks as $pluck) {
                $values = $result->pluck($pluck)->unique()->toArray();
                $values = array_filter($values);
                sort($values);
                $filters[$pluck]['values'] = $values;
            }
        }
        return array_values($filters);
    }
}
