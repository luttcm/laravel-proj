<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="icon" href="{{ asset('/img/logo.png') }}" type="image/x-icon">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-header">
            <div class="logo"></div>
            <div class="tagline"></div>
        </div>
        
        <div class="login-container">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="login-logo">
            <h1 class="login-title">Вход в систему</h1>
            
            @if ($errors->any())
                <div class="error-messages">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('auth') }}" autocomplete="off">
                @csrf
                
                <div class="form-group">
                    <label for="email" style="display: none;">Email адрес</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="your@email.com"
                        required 
                        autofocus
                    >
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password" style="display: none;">Пароль</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Введите пароль"
                        required
                    >
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-top: 12px; display: flex; align-items: center;">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        {{ old('remember') ? 'checked' : '' }}
                        style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;"
                    >
                    <label for="remember" style="margin: 0; cursor: pointer; font-size: 14px;">
                        Запомнить меня
                    </label>
                </div>
                
                <button type="submit" class="submit-btn">Вход</button>
            </form>
        </div>
        
        <div class="login-footer">
            <p>Proton © <?php echo date('Y'); ?></p>
        </div>
    </div>
</body>
</html>
