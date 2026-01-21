@extends('layouts.app')

@section('title', 'Переменные')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>НДС</h2>
        </div>
        <div class="col-md-4 text-end">
          <a href="{{ route('nds.add') }}" class="btn btn-primary">+ Добавить НДС</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h4 class="mt-5">НДС</h4>
    <div class="table-responsive mb-5">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Кодовое имя</th>
                    <th>Название</th>
                    <th>Значение</th>
                    <th style="width: 150px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nds as $ndsValue)
                    <tr>
                        <td>{{ $ndsValue->id }}</td>
                        <td><code>{{ $ndsValue->code_name }}</code></td>
                        <td>{{ $ndsValue->title }}</td>
                        <td>{{ $ndsValue->percent }}</td>
                        <td style="width: 150px;">
                            <div class="d-flex gap-2">
                                <a href="{{ route('nds.edit', ['id' => $ndsValue->id]) }}" class="btn btn-sm btn-outline-primary">Редактировать</a>
                                <form action="{{ route('nds.delete', ['id' => $ndsValue->id]) }}" method="POST" onsubmit="return confirm('Удалить?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Нет НДС
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($nds->hasPages())
        <nav class="d-flex justify-content-center mb-5">
            {{ $nds->links() }}
        </nav>
    @endif
</div>
@endsection