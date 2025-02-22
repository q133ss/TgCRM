<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    public function column(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Column::class, 'id', 'column_id');
    }
}
