@extends('layouts.app')

@section('title', 'Редактирование шутки')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-danger mb-0">✍️ Редактирование шутки</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('jokes.update', $joke) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="form-label text-danger">Название</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $joke->title) }}">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label text-danger">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description"
                                      rows="6">{{ old('description', $joke->description) }}</textarea>
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
                                        {{ old('id_category', $joke->id_category) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="rating" class="form-label text-danger">Оценка</label>
                            <input type="number" class="form-control @error('rating') is-invalid @enderror"
                                   id="rating" name="rating"
                                   value="{{ old('rating', $joke->rating) }}"
                                   step="0.1" min="0" max="5">
                            @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('jokes.show', $joke) }}" class="btn btn-dark">
                                Отменить изменения
                            </a>
                            <button type="submit" class="btn btn-danger">
                                Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
