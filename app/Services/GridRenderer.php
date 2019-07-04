<?php

namespace App\Services;

use App\Models\Datagrid;
use App\Services\Table;
use App\Models\Field;

class GridRenderer
{
    protected $grid;
    protected $params;
    protected $table;

    public function __construct(Datagrid $grid)
    {
        $this->grid = $grid;
        $this->table = new Table($grid);
        $this->params = request()->all();
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

    public function getHeaders()
    {
        return $this->table->getHeaders();
    }

    public function getBody($skipLinks = false)
    {
        return $this->table->getBody($skipLinks);
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
                    'values' => $values,
                    'is_default' => $field->has_default_filter == 1
                ];
                if ($field->has_default_filter) {
                    $filter['value'] = $field->default_filter_value;
                    if(strstr($field->default_filter_value, 'return ')) {
                        $filter['value'] = eval($field->default_filter_value);
                    }
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
                sort($values);
                $filters[$pluck]['values'] = $values;
            }
        }
        return array_values($filters);
    }
}
