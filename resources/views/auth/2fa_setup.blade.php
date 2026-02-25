@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white border-bottom-0 pt-4">
                    <h4 class="mb-0 text-center">Настройка двухфакторной аутентификации</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">
                        Отсканируйте этот QR-код вашим приложением для аутентификации (например, Google Authenticator) и введите полученный код ниже.
                    </p>

                    <div class="text-center mb-4">
                        {!! $qr_code_url !!}
                    </div>

                    <div class="text-center mb-4">
                        <small class="text-muted">Если вы не можете отсканировать код, введите этот секретный ключ вручную:</small>
                        <div class="mt-2">
                            <code class="bg-light p-2 rounded">{{ $secret }}</code>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('2fa.confirm') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="code" class="form-label">Код из приложения</label>
                            <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" required autofocus autocomplete="off">
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Подтвердить и включить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
