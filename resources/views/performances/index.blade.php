@extends('layouts.app')

@section('title', 'Выступления')

@section('content')
    <div class="min-vh-100 d-flex flex-column">
        <div class="flex-grow-1">
            @if($performances->isEmpty())
                <div class="alert alert-info text-center">
                    Выступлений пока нет. <a href="{{ route('performances.create') }}">Создать первое выступление!</a>
                </div>
            @else
                <div class="row">
                    @foreach($performances as $performance)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ $performance->title }}</h5>
                                    <small>{{ $performance->date->format('d.m.Y') }}</small>
                                </div>
                                <div class="card-body">
                                    <p>{{ Str::limit($performance->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Шуток: {{ $performance->jokes->count() }}</span>
                                        <a href="{{ route('performances.show', $performance) }}" class="btn btn-primary">Подробнее</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{ $performances->links() }}
            @endif
        </div>
    </div>
@endsection
