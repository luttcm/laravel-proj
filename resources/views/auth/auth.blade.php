<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background-color: #edeef0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #222;
}

.login-wrapper {
    width: 100%;
    max-width: 360px;
}

.login-header {
    text-align: center;
    margin-bottom: 16px;
}

.logo {
    font-size: 32px;
    font-weight: 700;
    color: #0077ff;
    margin-bottom: 6px;
}

.tagline {
    font-size: 13px;
    color: #6d7885;
}

.login-container {
    background: #fff;
    border-radius: 8px;
    padding: 24px;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
}

.login-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
}

.form-group {
    margin-bottom: 14px;
}

.form-group input {
    width: 100%;
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #d3d9de;
    border-radius: 6px;
    background-color: #fff;
}

.form-group input:focus {
    outline: none;
    border-color: #0077ff;
}

.error-messages {
    background-color: #fdeeee;
    border: 1px solid #f2b8b5;
    color: #b3261e;
    font-size: 13px;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 14px;
}

.error-message {
    font-size: 12px;
    color: #b3261e;
    margin-top: 4px;
}

.submit-btn {
    width: 100%;
    height: 36px;
    background-color: #0077ff;
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
}

.submit-btn:hover {
    background-color: #006ae6;
}

.demo-info {
    margin-top: 16px;
    font-size: 12px;
    color: #6d7885;
    background-color: #f5f6f8;
    border-radius: 6px;
    padding: 10px;
}

.login-footer {
    text-align: center;
    margin-top: 12px;
    font-size: 12px;
    color: #939393;
}
    </style>

</head>
<body>
    <div class="login-wrapper">
        <div class="login-header">
            <div class="logo"></div>
            <div class="tagline"></div>
        </div>
        
        <div class="login-container">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="me-2" style="height:55px; width: 55px;">
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
