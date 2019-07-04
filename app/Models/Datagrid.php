<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datagrid extends Model
{
    public $guarded = [];

    public function fields()
    {
        return $this->hasMany(Field::class, 'datagrid_id');
    }
}
