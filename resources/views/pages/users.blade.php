@extends('layouts.app')

@section('title', 'Пользователи')

@section('content')
<div class="container">
    <h1>Список пользователей</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th>Имя</th>
                <th>Email</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">Смотреть</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3">Пользователи не найдены</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection