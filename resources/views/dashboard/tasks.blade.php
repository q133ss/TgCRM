@extends('layouts.app')
@section('title', 'Мои задачи')
@section('content')
    <div class="card">
        <h5 class="card-header">Мои задачи</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead class="table-light">
                <tr>
                    <th>Задача</th>
                    <th>Дата</th>
                    <th>Колонка</th>
                    <th>Проект</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($tasks as $task)
                <tr>
                    <td><a href="{{route('dashboard.projects.show', $task->column?->project?->id)}}?task={{$task->id}}">{{$task->title}}</a></td>
                    <td>{{\Carbon\Carbon::parse($task->date)->format('d.m.Y')}}</td>
                    <td>{{$task->column?->title}}</td>
                    <td><a href="{{route('dashboard.projects.show', $task->column?->project?->id)}}">{{$task->column?->project?->title}}</a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
