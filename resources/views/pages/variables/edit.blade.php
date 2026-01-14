@extends('layouts.app')

@section('title', 'Редактировать переменную')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">Редактирование переменной</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

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

                    <form method="POST" action="{{ route('variable.update', ['id' => $variable->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Кодовое имя</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $variable->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Название</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $variable->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Тип</label>
                            <select name="type" class="form-select">
                                @foreach($types as $type)
                                    <option value="{{ $type }}" @if(old('type', $variable->type) == $type) selected @endif>{{ $type == "float" ? "Проценты" : "Целое"}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Тип таблицы</label>
                            <select name="table_type" class="form-select">
                                <option value="company" @if(old('table_type', $variable->table_type) == 'company') selected @endif>Переменные компании</option>
                                <option value="fnc" @if(old('table_type', $variable->table_type) == 'fnc') selected @endif>Переменные для ФНС</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Тип контрагента</label>
                            <select name="counteragent_type" class="form-select">
                                <option value="inn" @if(old('counteragent_type', $variable->counteragent_type) == 'inn') selected @endif>ИП (ИНН)</option>
                                <option value="ooo" @if(old('counteragent_type', $variable->counteragent_type) == 'ooo') selected @endif>ООО (УСН)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Значение</label>
                            <input type="text" name="value" class="form-control" value="{{ old('value', $variable->value) }}" required>
                        </div>

                        
                        <button class="btn btn-primary">Сохранить</button>
                        <a href="{{ route('variables.index') }}" class="btn btn-secondary">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
