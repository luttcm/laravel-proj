<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему - Calcul</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }
        
        .logo {
            font-size: 48px;
            font-weight: 700;
            letter-spacing: -2px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .tagline {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
            letter-spacing: 0.5px;
        }
        
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 45px 40px;
            backdrop-filter: blur(10px);
        }
        
        .login-title {
            text-align: center;
            color: #1a1a1a;
            margin-bottom: 35px;
            font-size: 24px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #667eea;
        }
        
        .form-group input {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid #e8e8e8;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: inherit;
            background-color: #f9f9f9;
        }
        
        .form-group input::placeholder {
            color: #bbb;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .error-messages {
            background-color: #fdeaea;
            border-left: 4px solid #dc3545;
            color: #dc3545;
            font-size: 13px;
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .error-messages p {
            margin: 0;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
        }
        
        .submit-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .submit-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.4);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
        
        .demo-info {
            background-color: #e8f4f8;
            border-left: 4px solid #0891b2;
            color: #0891b2;
            padding: 12px 14px;
            border-radius: 6px;
            margin-top: 30px;
            font-size: 12px;
            line-height: 1.6;
        }
        
        .demo-info strong {
            display: block;
            margin-bottom: 6px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 35px 25px;
            }
            
            .login-header {
                margin-bottom: 30px;
            }
            
            .logo {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-header">
            <div class="logo">Calcul</div>
            <div class="tagline">Система управления проектами</div>
        </div>
        
        <div class="login-container">
            <h1 class="login-title">Вход в систему</h1>
            
            @if ($errors->any())
                <div class="error-messages">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email адрес</label>
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
                    <label for="password">Пароль</label>
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
                
                <button type="submit" class="submit-btn">Вход</button>
            </form>
            
            <div class="demo-info">
                <strong>Демо учетные данные:</strong>
                Email: test@test.com<br>
                Пароль: password
            </div>
        </div>
        
        <div class="login-footer">
            <p>Calcul © 2025 • Система управления проектами</p>
        </div>
    </div>
</body>
</html>
