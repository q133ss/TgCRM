<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    protected $guarded = [];

    const DEFAULT_COLUMNS = [
        'Новые',
        'В работе',
        'На проверке',
        'Завершено'
    ];

    /**
     * @param $projectId
     * @return void
     *
     * Создает стандартные колонки для проекта
     */
    public function createDefault($projectId): void
    {
        $columns = [];
        foreach (self::DEFAULT_COLUMNS as $order => $title) {
            $columns[] = [
                'project_id' => $projectId,
                'title' => $title,
                'order' => $order,
                'created_at' => now()
            ];
        }
        Column::insert($columns);
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'column_id', 'id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }
}
