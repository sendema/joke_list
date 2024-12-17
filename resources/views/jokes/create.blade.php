@extends('layouts.app')

@section('title', 'Создание шутки')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-danger mb-0">Создание шутки</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('jokes.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="form-label text-danger">Название</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title') }}"
                                   placeholder="Как назовём этот шедевр?">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label text-danger">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="6"
                                      placeholder="Расскажите свою историю...">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="id_category" class="form-label text-danger">Категория</label>
                            <select class="form-select @error('id_category') is-invalid @enderror"
                                    id="id_category" name="id_category">
                                <option value="">Выберите категорию</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('id_category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="rating" class="form-label text-danger">Оценить</label>
                            <input type="number" class="form-control @error('rating') is-invalid @enderror"
                                   id="rating" name="rating" value="{{ old('rating', 0) }}"
                                   step="0.1" min="0" max="5"
                                   placeholder="От 0 до 5">
                            @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('jokes.index') }}" class="btn btn-dark">
                                К списку шуток
                            </a>
                            <button type="submit" class="btn btn-danger">
                                Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
