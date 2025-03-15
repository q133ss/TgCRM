@extends('layouts.app')
@section('title', 'Мои проекты')
@section('content')
    <div class="card">
        <h5 class="card-header">Мои проекты</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead class="table-light">
                <tr>
                    <th>Название</th>
                    <th>Дата создания</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @php $uidLink = request()->uid != null ? '?uid='.request()->uid : ''; @endphp
                @foreach($projects as $project)
                    <tr>
                        <td><a href="{{route('dashboard.projects.show', $project->id)}}{{$uidLink}}">{{$project->title}}</a></td>
                        <td>{{$project->created_at?->format('d.m.Y H:i')}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
