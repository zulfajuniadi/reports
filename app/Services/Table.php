<?php

namespace App\Services;

use App\Models\Datagrid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Field;
use App\Models\Report;
use Illuminate\Database\Query\Builder;

class Table
{
    protected $grid;
    protected $columnCount;
    protected $months = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Aug',
        9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dec',
    ];
    
    public function __construct(Datagrid $grid)
    {
        $this->grid = $grid;
    }

    protected function generateHead() : array
    {
        $headers =  [];
        foreach (collect($this->grid->fields)->sortBy('sort_order') as $field) {
            if ($field['is_shown']) {
                if ($field['is_sortable'] && !$this->grid->is_summary) {
                    $headers[] = [
                        'sort' => $field['sys_name'],
                        'content' => $field['name']
                    ];
                    $this->columnCount++;
                } else {
                    $headers[] = [
                        'content' => $field['name']
                    ];
                    $this->columnCount++;
                }
            }
        }
        if ($this->grid->is_summary) {
            if ($this->grid->summary_type == 'Month') {
                foreach ($this->months as $month) {
                    $headers[] = [
                        'content' => $field['name']
                    ];
                    $this->columnCount++;
                }
                if ($this->grid->has_sum_row) {
                    $headers[] = [
                        'content' => 'Sum'
                    ];
                    $this->columnCount++;
                }
            }
        }
        return $headers;
    }

    protected function buildQuery() : Builder
    {
        $rawQuery = DB::connection(config('database.report_db'))->table($this->grid->view_name);
        $selects = [];
        foreach (collect($this->grid->fields)->sortBy('sort_order') as $field) {
            if ($field['is_shown']) {
                $selects[] = $field['sys_name'];
            }
            if ($value = request()->get($field['sys_name'])) {
                if ($field['filter_type'] == 'Search') {
                    $value = strtolower($value);
                    $rawQuery->where($field['sys_name'], 'like', "%{$value}%");
                } elseif ($field['filter_type'] == 'Date Range') {
                    $rawQuery->whereBetween($field['sys_name'], $value);
                } elseif (is_array($value)) {
                    $rawQuery->whereIn($field['sys_name'], $value);
                } else {
                    $rawQuery->where($field['sys_name'], $value);
                }
            } elseif ($field['has_default_filter']) {
                $value = $field['default_filter_value'];
                if (strstr($value, 'return ')) {
                    $value = eval($field['default_filter_value']);
                }
                if ($field['filter_type'] == 'Search') {
                    $value = strtolower($value);
                    $rawQuery->where($field['sys_name'], 'like', "%{$value}%");
                } elseif ($field['filter_type'] == 'Date Range') {
                    $rawQuery->whereBetween($field['sys_name'], $value);
                } elseif (is_array($value)) {
                    $rawQuery->whereIn($field['sys_name'], $value);
                } else {
                    $rawQuery->where($field['sys_name'], $value);
                }
            }
        }

        if ($sortBy = request()->get('sort_by_column')) {
            $rawQuery->orderBy($sortBy, request()->get('sort_by_direction', 'asc'));
        } elseif (!$this->grid->is_summary) {
            if ($this->grid->sort_field_id) {
                $rawQuery->orderBy(Field::findOrFail($this->grid->sort_field_id)->sys_name, $this->grid->sort_direction);
            } else {
                foreach ($selects as $select) {
                    $rawQuery->orderBy($select);
                }
            }
        } else {
            foreach ($selects as $select) {
                $rawQuery->orderBy($select);
            }
        }

        $monthField = null;
        $countField = null;
        if ($this->grid->is_summary) {
            if ($this->grid->summary_type == 'Month') {
                $monthField = Field::findOrFail($this->grid->summary_by_column);
                $countField = Field::findOrFail($this->grid->summary_count_column);
                if (!in_array($monthField->sys_name, $selects)) {
                    $selects[] = $monthField->sys_name;
                }
                if (!in_array($countField->sys_name, $selects)) {
                    $selects[] = $countField->sys_name;
                }
            }
        }
        $rawQuery->select($selects);
        return $rawQuery;
    }

    protected function getSimpleGrid() : array
    {
        $rawDatas = $this->buildQuery()->get();
        $data = [];
        foreach ($rawDatas as $row) {
            $newRow = [
                'columns' => []
            ];
            foreach ($row as $column) {
                if (is_numeric($column)) {
                    $newRow['columns'][] = ['class' => 'text-right', 'content' => $column];
                } else {
                    $newRow['columns'][] = ['content' => $column];
                }
            }
            $data[] = $newRow;
        }
        if (count($data) == 0) {
            if ($this->columnCount == 0) {
                $this->generateHead();
            }
            return ['columns' => ['colspan' => $this->columnCount, 'class' => 'text-center font-italic', 'content' => 'No Data']];
        }
        return $data;
    }
    
    protected function generateBody()
    {
        if ($this->grid->is_summary) {
        }
        return $this->getSimpleGrid();
        // $data = [];
        // $firstSums = [];
        // $startAt = 0;
        // $lastFirstColumn = '';
        // if ($this->grid->is_summary) {
        //     if ($this->grid->summary_type == 'Month') {
        //         if ($this->grid->has_grand_sum_footer) {
        //             $grandSums = [];
        //             foreach ($this->months as $key => $month) {
        //                 $grandSums[$key] = 0;
        //             }
        //         }

        //         $link = '';
        //         if ($this->grid->has_drilldown == '1' && $this->grid->drilldown_report_id && $this->grid->drilldown_filters && $report = Report::find($this->grid->drilldown_report_id)) {
        //             $link = route('report', $report->slug);
        //         }

        //         $data = [];
        //         foreach ($rawDatas as $row) {
        //             // do i need to create a new row?
        //             $searches = [];
        //             foreach ($this->grid->fields()->orderBy('sort_order')->get() as $field) {
        //                 if ($field->is_shown) {
        //                     $searches[] = $row->{$field->sys_name};
        //                 }
        //             }
        //             if ($startAt == 0) {
        //                 $startAt = count($searches) - 1;
        //             }
        //             $searchStr = implode('.', $searches);
        //             $firstSearchStr = $searches[0];
        //             if (!isset($data[$searchStr])) {
        //                 // yes i do need to create a new row
        //                 $newRow = $searches;
        //                 foreach ($this->months as $month) {
        //                     $newValue = [];
        //                     if ($link) {
        //                         $newValue['link'] = $link;
        //                     }
        //                     $newValue['value'] = 0;
        //                     $newRow[] = $newValue;
        //                 }
        //                 if ($this->grid->has_sum_row) {
        //                     $newRow[] = 0;
        //                 }

        //                 if ($this->grid->has_sum_footer) {
        //                     if ($lastFirstColumn != $firstSearchStr) {
        //                         if ($lastFirstColumn != '') {
        //                             // append row
        //                             $newSumRow = [
        //                                 'Summary for ' . $lastFirstColumn
        //                             ];
        //                             foreach ($firstSums as $value) {
        //                                 $newSumRow[] = $value;
        //                             }
        //                             if ($this->grid->has_sum_row) {
        //                                 $newSumRow[]= array_sum($firstSums);
        //                             }
        //                             $data[$lastFirstColumn] = $newSumRow;
        //                         }
                                
        //                         // reset first sums
        //                         foreach ($this->months as $key => $month) {
        //                             $firstSums[$key] = 0;
        //                         }
                                
        //                         $lastFirstColumn = $firstSearchStr;
        //                     }
        //                 }

        //                 $data[$searchStr] = $newRow;
        //             }
        //             // row created, set data;
        //             $value = (int) $row->{$countField->sys_name};
        //             if ($this->grid->has_sum_row) {
        //                 $data[$searchStr][$startAt + 13] = $data[$searchStr][$startAt + 13] + $value;
        //             }
        //             $data[$searchStr][$startAt + $row->{$monthField->sys_name}]['value'] = $data[$searchStr][$startAt + $row->{$monthField->sys_name}]['value'] + $value;

        //             if ($this->grid->has_grand_sum_footer) {
        //                 $grandSums[$row->{$monthField->sys_name}] = $grandSums[$row->{$monthField->sys_name}] + $value;
        //             }

        //             if ($this->grid->has_sum_footer) {
        //                 $firstSums[$row->{$monthField->sys_name}] = $firstSums[$row->{$monthField->sys_name}] + $value;
        //             }
        //         }
        //         $data = array_values($data);

        //         if ($this->grid->has_sum_footer && count($data) > 0) {
        //             $newSumRow = [
        //                 'Summary for ' . $lastFirstColumn
        //             ];
        //             foreach ($firstSums as $value) {
        //                 $newSumRow[] = $value;
        //             }
        //             if ($this->grid->has_sum_row) {
        //                 $newSumRow[]= array_sum($firstSums);
        //             }
        //             $data[] = $newSumRow;
        //         }
        //     }
        // }

        // $this->tbody = [];
        // foreach ($data as $rowIndex => $row) {
        //     if (count($data) > 0) {
                // if ($this->grid->is_summary) {
                //     if (!is_array($row[0]) && stristr($row[0], 'Summary for')) {
                //         $this->tbody .= '<tr class="bg-warning">';
                //     } else {
                //         $this->tbody .= '<tr>';
                //     }
                //     $lastIndex = count($row) - 1;
                //     $hasChanged = false;
                //     foreach ($row as $columnIndex => $column) {
                //         $link = '';
                //         $month = $columnIndex - $startAt;
                //         $isMonth = $month >= 1 && $month <= 12;
                //         if (is_array($column)) {
                //             if (isset($column['link']) && !$skipLinks) {
                //                 $link = $column['link'];
                //                 $column = $column['value'];
                //                 $link = $link . '?' .  preg_replace('/\[\d+\]/', '[]', http_build_query(eval($this->grid->drilldown_filters), null, '&', PHP_QUERY_RFC3986));
                //                 $link = preg_replace('/(%5B)\d+(%5D=)/i', '$1$2', $link);
                //             } else {
                //                 $column = $column['value'];
                //             }
                //         }
                //         if (stristr($column, 'Summary for')) {
                //             $this->tbody .= '<td colspan="' . ($startAt + 1) . '">' . $column . '</td>';
                //         } elseif ($rowIndex > 0) {
                //             if ($hasChanged || is_numeric($column) || Arr::get($data, $rowIndex - 1 . '.' . $columnIndex) != $column) {
                //                 $hasChanged = true;
                //                 if ($columnIndex == $lastIndex && $this->grid->has_sum_row) {
                //                     $this->tbody .= '<td class="bg-info text-white text-right">' . $column . '</td>';
                //                 } elseif (is_numeric($column)) {
                //                     if ($link && $isMonth) {
                //                         $this->tbody .= '<td class="text-right"><a href="' . $link . '">' . $column . '</a></td>';
                //                     } else {
                //                         $this->tbody .= '<td class="text-right">' . $column . '</td>';
                //                     }
                //                 } else {
                //                     $this->tbody .= '<td>' . $column . '</td>';
                //                 }
                //             } else {
                //                 $this->tbody .= '<td></td>';
                //             }
                //         } else {
                //             if ($columnIndex == $lastIndex && $this->grid->has_sum_row) {
                //                 $this->tbody .= '<td class="bg-info text-white text-right">' . $column . '</td>';
                //             } elseif (is_numeric($column)) {
                //                 if ($link && $isMonth) {
                //                     $this->tbody .= '<td class="text-right"><a href="' . $link . '">' . $column . '</a></td>';
                //                 } else {
                //                     $this->tbody .= '<td class="text-right">' . $column . '</td>';
                //                 }
                //             } else {
                //                 $this->tbody .= '<td>' . $column . '</td>';
                //             }
                //         }
                //     }
                //     $this->tbody .= '</tr>';
                // }
        //     }
        // }

        // if ($this->grid->is_summary && count($data) > 0) {
        //     if ($this->grid->has_grand_sum_footer) {
        //         $this->tbody .= '<tr class="bg-primary text-white"><td colspan="' . ($startAt + 1) . '">Grand Summary</td>';
        //         $grandGrandSum = 0;
        //         foreach ($grandSums as $value) {
        //             $this->tbody .= '<td class="text-right">' . $value . '</td>';
        //             $grandGrandSum = $grandGrandSum + $value;
        //         }

        //         if ($this->grid->has_sum_row) {
        //             $this->tbody .= '<td class="text-right">' . $grandGrandSum . '</td>';
        //         }
        //         $this->tbody .= '</tr>';
        //     }
        // }
    }

    public function getHeaders()
    {
        return $this->generateHead();
    }

    public function getBody()
    {
        return $this->generateBody();
    }
}
