@extends('layouts.admin')
@section('title', 'Пользователи')
@section('content')
    <!-- Основной контент -->
    <div class="col-md-10 main-content">
        <h1 class="mt-3">Пользователи</h1>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Аватар</th>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Ник</th>
                <th>Telegram ID</th>
                <th>Статус подписки</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td><img src="{{$user->avatar_url}}" style="width: 50px; height: 50px; border-radius: 100%" alt="Аватар" class="img-thumbnail"></td>
                    <td>{{$user->first_name}}</td>
                    <td>{{$user->last_name}}</td>
                    <td>{{$user->username}}</td>
                    <td>{{$user->telegram_id}}</td>
                    <td><span class="badge bg-success">Активна</span></td>
                </tr>
            @endforeach
            <!-- Добавьте больше строк по мере необходимости -->
            </tbody>
        </table>
    </div>
@endsection
