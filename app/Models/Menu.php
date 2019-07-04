<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public $guarded = [];
    public $incrementing = false;

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }
}
