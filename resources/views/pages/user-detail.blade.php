@extends('layouts.app')

@section('title', 'Профиль пользователя')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">← Назад</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">{{ $user->name }}</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <strong>Email:</strong><br>
                        {{ $user->email }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Роль:</strong><br>
                        <span class="badge bg-info">{{ $user->role ?? 'user' }}</span>
                    </p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <strong>Создано:</strong><br>
                        {{ $user->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Обновлено:</strong><br>
                        {{ $user->updated_at->format('d.m.Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-warning">Редактировать</button>
            <button class="btn btn-danger">Удалить</button>
        </div>
    </div>
@endsection
