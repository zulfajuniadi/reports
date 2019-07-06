<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Okipa\LaravelModelJsonStorage\ModelJsonStorage;

class Report extends Model
{
    use ModelJsonStorage;
    public $guarded = [];
}
