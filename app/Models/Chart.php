<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Okipa\LaravelModelJsonStorage\ModelJsonStorage;

class Chart extends Model
{
    use ModelJsonStorage;
    protected $guarded = [];
}
