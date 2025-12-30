@extends('layouts.app')

@section('title', 'Переменные')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Переменные</h2>
        </div>
        <div class="col-md-4 text-end">
          <a href="{{ route('variable.add') }}" class="btn btn-primary">+ Добавить переменную</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h4 class="mt-5">Переменные компании</h4>
    <div class="table-responsive mb-5">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Значение</th>
                    <th style="width: 150px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companyVariables as $variable)
                    <tr>
                        <td>{{ $variable->id }}</td>
                        <td>{{ $variable->name }}</td>
                        <td>{{ $variable->type ==  "float" ? "Дробное" : "Целое"}}</td>
                        <td>{{ $variable->value }}</td>
                        <td style="width: 150px;">
                            <div class="d-flex gap-2">
                                <a href="{{ route('variable.edit', ['id' => $variable->id]) }}" class="btn btn-sm btn-outline-primary">Редактировать</a>
                                <form action="{{ route('variable.delete', ['id' => $variable->id]) }}" method="POST" onsubmit="return confirm('Удалить?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Нет переменных
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($companyVariables->hasPages())
        <div class="d-flex justify-content-center mb-5">
            {{ $companyVariables->links() }}
        </div>
    @endif

    <h4 class="mt-5">Переменные для ФНС</h4>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Значение</th>
                    <th style="width: 150px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fncVariables as $variable)
                    <tr>
                        <td>{{ $variable->id }}</td>
                        <td style="max-width: 400px;">{{ $variable->name }}</td>
                        <td>{{ $variable->type ==  "float" ? "Дробное" : "Целое"}}</td>
                        <td>{{ $variable->value }}</td>
                        <td style="width: 150px;">
                            <div class="d-flex gap-2">
                                <a href="{{ route('variable.edit', ['id' => $variable->id]) }}" class="btn btn-sm btn-outline-primary">Редактировать</a>
                                <form action="{{ route('variable.delete', ['id' => $variable->id]) }}" method="POST" onsubmit="return confirm('Удалить?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Нет переменных
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($fncVariables->hasPages())
        <div class="d-flex justify-content-center">
            {{ $fncVariables->links() }}
        </div>
    @endif
</div>
@endsection