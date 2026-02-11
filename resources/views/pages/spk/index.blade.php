@extends('layouts.app')

@section('title', 'СПК')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>СПК</h2>
        </div>
        <div class="col-md-4 text-end">
          <a href="{{ route('spk.add') }}" class="btn btn-primary">+ Добавить СПК</a>
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
                    <th>ФИО</th>
                    <th>Коэффициент</th>
                    <th style="width: 150px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($spks as $spk)
                    <tr>
                        <td>{{ $spk->id }}</td>
                        <td>{{ $spk->fio }}</td>
                        <td>{{ $spk->coefficient }}</td>
                        <td style="width: 150px;">
                            <div class="d-flex gap-2">
                                <a href="{{ route('spk.edit', ['id' => $spk->id]) }}" class="btn btn-sm btn-outline-primary">Редактировать</a>
                                <form action="{{ route('spk.delete', ['id' => $spk->id]) }}" method="POST" onsubmit="return confirm('Удалить?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Нет записей СПК
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($spks->hasPages())
        <nav class="d-flex justify-content-center mb-5">
            {{ $spks->links() }}
        </nav>
    @endif
</div>
@endsection
