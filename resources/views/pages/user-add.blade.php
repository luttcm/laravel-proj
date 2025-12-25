@extends('layouts.app')

@section('title', 'Добавить пользователя')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Добавить нового пользователя</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Имя</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Роль</label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="">Выберите роль</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" value="{{ old('password') }}" placeholder="Оставить пусто для автогенерирования">
                                <button class="btn btn-outline-secondary" type="button" id="generateBtn">
                                    Сгенерировать
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Минимум 8 символов или оставьте пусто для случайного пароля</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Создать пользователя</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary w-100 mt-2">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('generateBtn').addEventListener('click', function() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
            let password = '';
            for (let i = 0; i < 12; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('password').value = password;
        });
    </script>
@endsection
