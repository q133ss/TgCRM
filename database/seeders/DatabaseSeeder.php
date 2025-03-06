<?php

namespace Database\Seeders;

use App\Models\Column;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // . Администратор проекта (Admin)
        //Обладает почти такими же правами, как владелец, но не может передать роль владельца другому пользователю.
        //Может управлять настройками проекта, например, изменять права доступа, переименовывать доски, удалять задачи.
        //Может приглашать и исключать участников.

        //3. Редактор (Editor)
        //Может создавать, редактировать и удалять задачи, карточки и комментарии.
        //Может перемещать карточки между колонками.
        //Не имеет права изменять общие настройки проекта или управлять другими пользователями.

        //4. Читатель (Viewer/Reader)
        //Может только просматривать содержимое проекта: доски, задачи, комментарии.
        //Не может создавать, редактировать или удалять что-либо.
        //Полезная роль для тех, кто хочет быть в курсе происходящего, но не участвует непосредственно в работе.

        $roles = [
            'owner' => 'Владелец',
            'admin' => 'Администратор',
            'editor' => 'Редактор',
            'viewer' => 'Читатель'
        ];

        foreach ($roles as $slug => $name) {
            Role::create(['slug' => $slug, 'name' => $name]);
        }

        // admin user
        User::create([
            'username' => 'admin',
            'is_admin' => true
        ]);

        // for local
        if(app()->isLocal()):
            $user = User::create([
                'username' => 'miroshkin222',
                'telegram_id' => '461612832',
                'first_name' => 'Alexey',
                'last_name' => 'Miroshkin',
                'avatar_url' => 'https://api.telegram.org/file/bot7842393247:AAECe2wZ_BqSWzbNnIkR-Cri6c1KZabKsPU/photos/file_21.jpg'
            ]);

            $project = Project::create([
                'chat_id' => '461612832',
                'title' => 'Alexey',
                'created_by' => $user->id
            ]);

            DB::table('project_users')->insert([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'role_id' => Role::where('slug', 'owner')->pluck('id')->first()
            ]);

            Column::insert([
                [
                    'project_id' => $project->id,
                    'title' => 'Новые',
                    'order' => 0,
                ],
                [
                    'project_id' => $project->id,
                    'title' => 'В работе',
                    'order' => 1,
                ],
                [
                    'project_id' => $project->id,
                    'title' => 'На проверке',
                    'order' => 2,
                ],
                [
                    'project_id' => $project->id,
                    'title' => 'Завершено',
                    'order' => 3,
                ]
            ]);
        endif;
    }
}
