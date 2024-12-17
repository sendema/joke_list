<!-- resources/views/performances/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Редактировать выступление')

@section('styles')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Редактировать выступление</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('performances.update', $performance) }}" method="POST" enctype="multipart/form-data">                @csrf
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Название выступления</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title', $performance->title) }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3">{{ old('description', $performance->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Дата выступления</label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                           id="date" name="date" value="{{ old('date', $performance->date->format('Y-m-d')) }}" required>
                    @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <input type="hidden" name="audio_id" value="{{ $performance->audio_id }}">
                <input type="hidden" name="video_id" value="{{ $performance->video_id }}">
                <div class="mb-3">
                    <label for="script" class="form-label">Текст выступления</label>
                    <textarea class="form-control @error('script') is-invalid @enderror"
                              id="script" name="script">{{ old('script', $performance->script) }}</textarea>
                    @error('script')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="new_audio">Новое аудио</label>
                    <input type="file" name="new_audio" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="new_video">Новое видео</label>
                    <input type="file" name="new_video" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Шутки в программе</label>
                    <div class="list-group">
                        @foreach($performance->jokes as $joke)
                            <div class="list-group-item bg-dark text-white border-danger d-flex justify-content-between align-items-center">
                                {{ $joke->title }}
                                <form action="{{ route('performances.jokes.remove', ['performance' => $performance, 'joke' => $joke]) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label for="jokes" class="form-label">Добавить шутки</label>
                    <select multiple class="form-control @error('jokes') is-invalid @enderror" id="jokes" name="jokes[]">
                        @foreach($userJokes as $joke)
                            @if(!$performance->jokes->contains($joke->id))
                                <option value="{{ $joke->id }}">{{ $joke->title }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('jokes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Зажмите Ctrl (Cmd на Mac) для выбора нескольких шуток</small>
                </div>

                <div id="jokes-order-inputs">
                    @foreach($performance->jokes as $index => $joke)
                        <input type="hidden" name="jokes_order[]" value="{{ $index }}">
                    @endforeach
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('performances.show', $performance) }}" class="btn btn-secondary">Назад</a>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            ClassicEditor
                .create(document.querySelector('#script'), {
                    toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
                })
                .catch(error => {
                    console.error(error);
                });
            document.getElementById('jokes').addEventListener('change', function() {
                const orderInputs = document.getElementById('jokes-order-inputs');
                orderInputs.innerHTML = '';
                const selectedOptions = Array.from(this.selectedOptions);
                selectedOptions.forEach((option, index) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'jokes_order[]';
                    input.value = index;
                    orderInputs.appendChild(input);
                });
            });
        </script>
    @endpush
@endsection

