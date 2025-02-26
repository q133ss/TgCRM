<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];

    public function columns()
    {
        return $this->hasMany(Column::class, 'project_id', 'id')->orderBy('order');
    }
}
