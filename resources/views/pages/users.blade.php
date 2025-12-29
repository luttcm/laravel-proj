@extends('layouts.app')

@section('title', 'Пользователи')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Пользователи</h2>
        </div>
        <div class="col-md-4 text-end">
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
                <a href="{{ route('user.add') }}" class="btn btn-primary">+ Добавить пользователя</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roleNames = [
                                    'admin' => 'Администратор',
                                    'manager' => 'Менеджер',
                                    'redactor' => 'Редактор',
                                    'finance' => 'Финансовый директор',
                                    'user' => 'Пользователь',
                                ];
                                $displayName = $roleNames[$user->role] ?? ucfirst($user->role);
                            @endphp
                            <span class="badge bg-info">{{ $displayName }}</span>
                        </td>
                        <td>
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                Просмотр
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Нет пользователей
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection