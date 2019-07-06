<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Okipa\LaravelModelJsonStorage\ModelJsonStorage;

class Datagrid extends Model
{
    use ModelJsonStorage;
    public $guarded = [];
}
