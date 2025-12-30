@extends('layouts.app')

@section('title', 'Редактировать пользователя')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">Редактирование пользователя</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('users.update', ['id' => $user->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Имя</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Роль</label>
                            <select name="role" class="form-select">
                                @foreach($roles as $r)
                                    @php
                                        $roleNames = [
                                            'admin' => 'Администратор',
                                            'manager' => 'Модератор',
                                            'redactor' => 'Редактор',
                                            'finance' => 'Финансовый директор',
                                            'user' => 'Пользователь',
                                        ];
                                        $displayName = $roleNames[$r] ?? ucfirst($r);
                                    @endphp
                                    <option value="{{ $r }}" @if(old('role', $user->role) == $r) selected @endif>{{ $displayName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button class="btn btn-primary">Сохранить</button>
                        <a href="{{ route('users.show', ['id' => $user->id]) }}" class="btn btn-secondary">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
