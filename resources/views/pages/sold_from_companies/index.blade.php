@extends('layouts.app')

@section('title', 'Компании-продавцы (ОТ КОГО)')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Компании-продавцы (ОТ КОГО)</h2>
        </div>
        <div class="col-md-4 text-end">
          <a href="{{ route('sold-from-companies.add') }}" class="btn btn-primary">+ Добавить компанию</a>
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
                    <th style="width: 250px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td>{{ $company->id }}</td>
                        <td>{{ $company->name }}</td>
                        <td style="width: 250px;">
                            <div class="d-flex gap-2">
                                <a href="{{ route('sold-from-companies.edit', $company->id) }}" class="btn btn-sm btn-outline-primary">Редактировать</a>
                                <form action="{{ route('sold-from-companies.delete', $company->id) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить компанию?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            Нет записей о компаниях-продавцах
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($companies->hasPages())
        <nav class="d-flex justify-content-center mb-5">
            {{ $companies->links() }}
        </nav>
    @endif
</div>
@endsection
