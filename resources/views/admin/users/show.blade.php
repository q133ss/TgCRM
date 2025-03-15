@extends('layouts.admin')
@section('title', 'Пользователь')
@section('content')
    <!-- Основной контент -->
    <div class="col-md-10 main-content">
        <h1 class="mt-3">Пользователь - {{$user->username}}</h1>

        <div class="user_details">
            <span><img src="{{$user->avatar_url}}" style="width: 50px; height: 50px; border-radius: 100%" alt="Аватар" class="img-thumbnail"></span>
            <br>
            <span>ID: {{$user->id}}</span> <br>
            <span>Имя: {{$user->first_name}}</span> <br>
            <span>Фамилия: {{$user->last_name}}</span> <br>
            <span>Логин: {{$user->username}}</span> <br>
            <span>TG ID: {{$user->telegram_id}}</span> <br>
            <span>Подписка: <span class="badge bg-success">Активна</span></span>
        </div>

        <h3>Проекты</h3>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Название</th>
            </tr>
            </thead>
            <tbody>
            @foreach($user->projects as $project)
                <tr style="cursor:pointer;" onclick="location.href='{{route('admin.projects.show', $project->id)}}'">
                    <td>{{$project->id}}</td>
                    <td>{{$project->title}}</td>
                </tr>
            @endforeach
            <!-- Добавьте больше строк по мере необходимости -->
            </tbody>
        </table>
    </div>
@endsection
