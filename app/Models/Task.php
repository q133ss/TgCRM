<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];
    protected $with = ['responsible'];

    public function column(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Column::class, 'id', 'column_id');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function responsible()
    {
        return $this->belongsToMany(User::class, 'task_responsibles');
    }
}
