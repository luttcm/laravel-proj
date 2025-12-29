@extends('layouts.app')

@section('title', auth()->user()->name)

@section('content')
@php
$roleNames = [
    'admin' => 'Администратор',
    'manager' => 'Модератор',
    'redactor' => 'Редактор',
    'finance' => 'Финансовый директор',
    'user' => 'Пользователь',
];
$displayName = $roleNames[$user->role] ?? ucfirst($user->role);
@endphp
<div class="container" style="max-width: 600px;">
    <div style="padding: 12px 0;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Profile Info Card -->
    <div class="profile-info-block">
        <img src="{{ auth()->user()->avatar }}" alt="avatar" class="profile-avatar">
        <h3 style="margin: 16px 0 8px 0; font-weight: 600;">{{ $user->name }}</h3>
        <p style="color: #999; margin: 0; font-size: 0.95rem;">{{ $user->role ?? 'user' }}</p>
    </div>

    <!-- Edit Name Card -->
    <div class="profile-card">
        <div class="profile-card-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Имя</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Email</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" readonly disabled style="background-color: #f9f9f9;">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Роль</label>
                    <input type="text" class="form-control" value="{{ $displayName }}" readonly disabled style="background-color: #f9f9f9;">
                    <input type="hidden" value="{{ $user->role }}">
                </div>
                <button type="submit" class="btn btn-primary w-100" style="border-radius: 6px;">Сохранить изменения</button>
            </form>
        </div>
    </div>

    <!-- Avatar Upload Card -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h6 style="margin: 0; font-weight: 600;">Фото профиля</h6>
        </div>
        <div class="profile-card-body">
            <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" id="avatarForm">
                @csrf
                <div class="mb-3">
                    <input type="file" name="avatar" accept="image/*" class="form-control" id="avatarInput">
                    <small style="color: #999;">Рекомендуемый размер: 500x500px</small>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="border-radius: 6px;">Загрузить аватар</button>
            </form>
        </div>
    </div>
</div>
@endsection
