<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'telegram_id',
        'first_name',
        'last_name',
        'avatar_url'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_users');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_responsibles');
    }

    public function getCountForFront(string $type)
    {
        switch ($type){
            case 'projects':
                return $this->pluralize($this->projects?->count(), ['проект', 'проекта', 'проектов']);
            case 'tasks':
                return $this->pluralize($this->tasks?->count(), ['задача', 'задачи', 'задач']);
            default:
                return 'Ошибка';
        }
    }

    private function pluralize($number, $words)
    {
        $cases = [2, 0, 1, 1, 1, 2];
        return $number . ' ' . $words[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }
}
