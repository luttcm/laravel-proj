@extends('layouts.app')

@section('title', 'Добавить переменную')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Добавить новый НДС</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('nds.store') }}">
                        @csrf
    
                        <div class="mb-3">
                            <label for="code_name" class="form-label">Кодовое имя</label>
                            <input type="text" class="form-control @error('code_name') is-invalid @enderror" 
                                   id="code_name" name="code_name" value="{{ old('code_name') }}" placeholder="Например: nds_10 или nds_20" required>
                            @error('code_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Название</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" placeholder="Для ясности введения переменной" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="percent" class="form-label">Значение</label>
                            <input type="text" class="form-control @error('percent') is-invalid @enderror" 
                                   id="percent" name="percent" value="{{ old('percent') }}" required>
                            @error('percent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Создать НДС</button>
                        <a href="{{ route('nds.index') }}" class="btn btn-secondary w-100 mt-2">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
