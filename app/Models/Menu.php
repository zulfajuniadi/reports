<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Okipa\LaravelModelJsonStorage\ModelJsonStorage;

class Menu extends Model
{
    use ModelJsonStorage;
    public $guarded = [];
    public $incrementing = false;

    public function children()
    {
        return app(static::class)->where('parent_id', $this->id)->get()->map(function($data){
            return new static($data);
        });
    }
}
