@extends('layouts.app')

@section('title', 'Добавить переменную')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Добавить новую переменную</h4>
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

                    <form method="POST" action="{{ route('variables.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Название</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Тип</label>
                            <select type="text" class="form-control @error('type') is-invalid @enderror" 
                                   id="type" name="type" required>
                                   @foreach($types as $type)
                                   {{ $typeName = $type == "float" ? "Дробное" : "Целое" }}
                                    <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                                        {{ ucfirst($typeName) }}
                                    </option>
                                    @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="table_type" class="form-label">Тип таблицы</label>
                            <select class="form-control @error('table_type') is-invalid @enderror" 
                                   id="table_type" name="table_type" required>
                                    <option value="company" {{ old('table_type') === 'company' ? 'selected' : '' }}>Переменные компании</option>
                                    <option value="fnc" {{ old('table_type') === 'fnc' ? 'selected' : '' }}>Переменные для ФНС</option>
                            </select>
                            @error('table_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="value" class="form-label">Значение</label>
                            <input type="text" class="form-control @error('value') is-invalid @enderror" 
                                   id="value" name="value" value="{{ old('value') }}" required>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Создать переменную</button>
                        <a href="{{ route('variables.index') }}" class="btn btn-secondary w-100 mt-2">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
