<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Calculator')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('app/public/img/logo.png') }}" type="image/x-icon">
    <style>
body {
    padding-top: 70px;
    background-color: #f2f3f5;
    color: #212529;
}
.nav-item {
    padding: 1% 0% 1% 0;
}
.nav-item:hover {
    background-color: #f2f3f5c5;
}
nav.navbar.navbar-expand-lg.navbar-light.bg-white.fixed-top.shadow-sm {
    padding: 0 12px;
    min-height: 64px;
    max-height: 75px;
}
ul.dropdown-menu.dropdown-menu-end.show {
    padding: 0;
}
.navbar-brand { font-size: 1.05rem; }
.navbar-nav .nav-link { padding: 12px 14px; }
.dropdown-menu {
    border-radius: 0 !important;
    box-shadow: 0 2px 12px rgba(0,0,0,0.10);
    border: 1px solid #e5e5e5;
    padding: 0;
}
.dropdown-item {
    border-radius: 0 !important;
    padding: 12px 18px;
    transition: background 0.15s;
}
.dropdown-item:hover {
    background: #f2f3f5;
}
.dropdown-divider { margin: 0; }
.navbar img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 8px;
    object-fit: cover;
}
.navbar-collapse .nav-link {
    padding: 10px 16px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}
.profile-card {
    background-color: #fff;
    border: none;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    margin-bottom: 16px;
    transition: box-shadow 0.2s ease;
}
.profile-card:hover {
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
}
.profile-card-header {
    background-color: transparent;
    border-bottom: 1px solid #f0f0f0;
    padding: 16px;
}
.profile-card-body {
    padding: 16px;
}
.profile-card-footer {
    background-color: #f9f9f9;
    border-top: 1px solid #f0f0f0;
    padding: 16px;
    border-radius: 0 0 8px 8px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.profile-info-block {
    background-color: #fff;
    border-radius: 8px;
    padding: 24px 16px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    margin-bottom: 16px;
}
.profile-avatar {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}
.news-feed {
    max-width: 600px;
    margin: 0 auto;
}
.news-card {
    background: #fff;
    border-radius: 8px;
    border: 1px solid #dce1e6;
    margin-bottom: 16px;
    overflow: hidden;
}
.news-card-header {
    padding: 12px 16px;
    border-bottom: 1px solid #dce1e6;
}
.news-title {
    font-size: 16px;
    font-weight: 600;
    color: #222;
    margin: 0;
}
.news-image-container {
    width: 100%;
    aspect-ratio: 1 / 1;
    overflow: hidden;
}
.news-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.news-card-text {
    padding: 12px 16px;
}
.news-text {
    font-size: 14px;
    color: #333;
    line-height: 1.5;
    margin: 0;
}
.news-card-footer {
    padding: 8px 12px;
    border-top: 1px solid #dce1e6;
    display: flex;
    gap: 8px;
    align-items: center;
}
.news-actions .btn {
    border: none;
    background: transparent;
    font-size: 13px;
    color: #626d7a;
    padding: 6px 8px;
    flex: 1;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 4px;
}
.news-actions .btn:hover {
    color: #0077ff;
    border-radius: 4px;
}
.news-add-btn {
    background-color: #0077ff;
    border: none;
    font-size: 14px;
    padding: 8px 16px;
    color: #fff;
}
.news-add-btn:hover {
    background-color: #006ae6;
}
@media (max-width: 990px) {
    body {
        padding-top: 60px;
    }
    .navbar-collapse .nav-link:hover {
        background-color: #f2f3f5;
    }
    .navbar-collapse {
        background-color: #fff;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        min-width: 100vw;
        left: -12px;
        position: relative;
        margin-left: -12px;
        margin-right: -12px;
        padding-bottom: 12px;
    }
    .navbar {
        width: 100vw;
        left: 0;
        right: 0;
    }
}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('app/public/img/logo.png') }}" alt="Logo" class="me-2" style="height:55px; width: 55px;">
                <b>PROTON</b>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Страница менеджеров</a>
                    </li>
                    @if (auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="#">Страница финансового директора</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('news.index') }}">Новости</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar }}">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">Профиль</a>
                            </li>
                            @if (auth()->user()->role === 'admin')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.index') }}">Пользователи</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @if(auth()->user() && in_array(auth()->user()->role, ['admin','redactor']))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('news.create') }}">Добавить новость</a>
                                    </li>
                                @endif
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Выход</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
