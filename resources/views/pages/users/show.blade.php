@extends('layouts.app')

@section('title', 'Профиль пользователя')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>{{ $user->name }}</h3>
            </div>
            <div class="card-body">
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Создан:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Назад</a>
            </div>
        </div>
    </div>
</div>
@endsection
