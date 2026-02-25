@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white border-bottom-0 pt-4 text-center">
                    <h4 class="mb-0">Двухфакторная аутентификация</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">
                        Введите 6-значный код из вашего приложения для аутентификации.
                    </p>

                    <form method="POST" action="{{ route('2fa.verify.post') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="one_time_password" class="form-label">Код подтверждения</label>
                            <input id="one_time_password" type="text" class="form-control @error('one_time_password') is-invalid @enderror" name="one_time_password" required autofocus autocomplete="off">
                            @error('one_time_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Войти
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
