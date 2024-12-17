@extends('layouts.app')

@section('title', 'Мои шутки')

@section('content')
    <div class="container mt-4">
        @if($jokes->count() > 0)
            @if(Route::currentRouteName() == 'jokes.index')
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <form action="{{ route('jokes.index') }}" method="GET">
                            <!-- Поиск с кнопкой фильтров -->
                            <div class="input-group mb-4">
                                <input type="text"
                                       name="search"
                                       class="form-control bg-dark text-white border-danger"
                                       placeholder="Поиск по названию шутки..."
                                       value="{{ request('search') }}"
                                       style="border-right: none;">
                                <button class="btn btn-outline-danger" type="submit">
                                    🔍 Найти
                                </button>
                                <button type="button"
                                        class="btn btn-outline-danger ms-2 d-flex align-items-center gap-2 filter-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#filtersModal">
                                    <i class="fas fa-filter"></i>
                                    <span>Фильтры</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endif
    </div>
{{--@endsection--}}


<!-- Модальное окно с фильтрами -->
    <div class="modal fade"
         id="filtersModal"
         tabindex="-1"
         data-bs-backdrop="static"
         aria-labelledby="filtersModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-white" id="filtersModalLabel">Фильтры</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('jokes.index') }}" method="GET">
                    <div class="modal-body">
                        <!-- Сохраняем значение поиска -->
                        <input type="hidden" name="search" value="{{ request('search') }}">

                        <!-- Категории -->
                        <div class="mb-3">
                            <label class="form-label text-white">Категория</label>
                            <select name="category"
                                    class="form-select bg-dark text-white border-danger cursor-pointer"
                                    autocomplete="off">
                                <option value="">Все категории</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Рейтинг -->
                        <div class="mb-3">
                            <label class="form-label text-white">Рейтинг</label>
                            <select name="rating"
                                    class="form-select bg-dark text-white border-danger cursor-pointer"
                                    autocomplete="off">
                                <option value="">Любой рейтинг</option>
                                @foreach($ratings as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ request('rating') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Сортировка по дате -->
                        <div class="mb-3">
                            <label class="form-label text-white">Сортировка по дате</label>
                            <select name="date_sort"
                                    class="form-select bg-dark text-white border-danger cursor-pointer"
                                    autocomplete="off">
                                <option value="desc" {{ request('date_sort', 'desc') == 'desc' ? 'selected' : '' }}>
                                    Сначала новые
                                </option>
                                <option value="asc" {{ request('date_sort') == 'asc' ? 'selected' : '' }}>
                                    Сначала старые
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-danger">
                        @if(request()->hasAny(['category', 'rating', 'date_sort']))
                            <a href="{{ route('jokes.index', ['search' => request('search')]) }}"
                               class="btn btn-outline-danger"
                               onclick="return confirm('Сбросить все фильтры?')">
                                Сбросить фильтры
                            </a>
                        @endif
                        <button type="submit" class="btn btn-danger">Применить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Список шуток -->
    <div class="row">
        @if($jokes->count() > 0)
            @foreach($jokes as $joke)
                <div class="col-md-6 mb-4">
                    <div class="card joke-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $joke->title }}</h5>
                            <span class="badge bg-danger">★ {{ number_format($joke->rating, 1) }}</span>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ Str::limit($joke->description, 150) }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-danger">
                                        {{ $joke->user->name }}
                                        <span class="text-muted"> • {{ $joke->created_at->format('d M Y') }}</span>
                                    </small>
                                    <span class="badge bg-dark">{{ $joke->category->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-danger">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('jokes.show', $joke) }}" class="btn btn-outline-danger btn-sm">
                                    Открыть
                                </a>
                                <div class="btn-group">
                                    <a href="{{ route('jokes.edit', $joke) }}" class="btn btn-outline-warning btn-sm">
                                        Изменить
                                    </a>
                                    <form action="{{ route('jokes.destroy', $joke) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Точно удаляем шутку?')">
                                            Удалить
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="alert alert-info text-center">
                    @if(request()->hasAny(['search', 'category', 'rating', 'date_sort']))
                        По заданным критериям шутки не найдены.
                        <a href="{{ route('jokes.index') }}" class="alert-link">Показать все шутки</a>
                    @else
                        У вас пока нет шуток.
                        <a href="{{ route('jokes.create') }}" class="alert-link">Создайте свою первую шутку!</a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Пагинация -->
    @if($jokes->count() > 0)
        <div class="d-flex justify-content-center mt-4">
            {{ $jokes->links() }}
        </div>
    @endif

@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.7);
        }

        .form-select {
            cursor: pointer;
        }

        .modal .form-select:focus,
        .modal .form-control:focus {
            border-color: #ff4444;
            box-shadow: 0 0 0 0.2rem rgba(255, 68, 68, 0.25);
        }

        .modal-content {
            border: 1px solid #ff4444;
        }

        /* Добавляем стили для кнопки фильтров */
        .filter-btn {
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
        }

        .filter-btn i {
            transition: transform 0.3s ease;
        }

        .filter-btn:hover i {
            transform: rotate(180deg);
        }
    </style>
@endpush
