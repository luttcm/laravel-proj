@extends('layouts.app')

@section('title', 'Поставщики')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Поставщики</h2>
        </div>
        <div class="col-md-4 text-end">
          <a href="{{ route('supplier.add') }}" class="btn btn-primary">+ Добавить поставщика</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive mb-5">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>НДС (%)</th>
                    <th style="width: 150px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->vat }}</td>
                        <td style="width: 150px;">
                            <div class="d-flex gap-2">
                                <a href="{{ route('supplier.edit', ['id' => $supplier->id]) }}" class="btn btn-sm btn-outline-primary">Редактировать</a>
                                <form action="{{ route('supplier.delete', ['id' => $supplier->id]) }}" method="POST" onsubmit="return confirm('Удалить?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Нет записей о поставщиках
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
        <nav class="d-flex justify-content-center mb-5">
            {{ $suppliers->links() }}
        </nav>
    @endif
</div>
@endsection
