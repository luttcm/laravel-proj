@extends('layouts.app')

@section('title', 'Редактировать переменную')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">Редактирование НДС</div>
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

                    <form method="POST" action="{{ route('nds.update', ['id' => $nds->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Кодовое имя</label>
                            <input type="text" name="code_name" class="form-control" value="{{ old('code_name', $nds->code_name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Название</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $nds->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Значение</label>
                            <input type="text" name="percent" class="form-control" value="{{ old('percent', $nds->percent) }}" required>
                        </div>

                        
                        <button class="btn btn-primary">Сохранить</button>
                        <a href="{{ route('nds.index') }}" class="btn btn-secondary">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
