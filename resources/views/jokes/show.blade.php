@extends('layouts.app')

@section('title', $joke->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="text-danger mb-0">{{ $joke->title }}</h2>
                        <div class="rating">
                        <span class="badge bg-danger fs-6">
                            ★ {{ number_format($joke->rating, 1) }}
                        </span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="performer-info mb-4">
                        <div class="d-flex align-items-center">
                            <div class="performer-avatar bg-danger text-white rounded-circle p-3 me-3">
                                {{ strtoupper(substr($joke->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="text-danger mb-1">{{ $joke->user->name }}</h5>
                                <small class="text-muted">
                                    {{ $joke->created_at->format('d M Y, H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="joke-content mb-4">
                        <p class="fs-5">{{ $joke->description }}</p>
                    </div>

                    <div class="joke-meta">
                        <span class="badge bg-dark me-2">{{ $joke->category->name }}</span>
                    </div>

                    @if(Auth::id() === $joke->user_id)
                        <div class="mt-4 pt-4 border-top">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('jokes.edit', $joke) }}" class="btn btn-warning">
                                    Изменить шутку
                                </a>
                                <form action="{{ route('jokes.destroy', $joke) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Уверены, что хотите удалить эту шутку?')">
                                        Удалить шутку
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('jokes.index') }}" class="btn btn-dark">
                        Вернуться к шуткам
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
@endsection
