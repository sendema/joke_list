<!-- resources/views/performances/create.blade.php -->
@extends('layouts.app')

@section('title', 'Создать выступление')

@section('styles')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Создать новое выступление</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('performances.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Название выступления</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Дата выступления</label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                           id="date" name="date" value="{{ old('date') }}" required>
                    @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="script" class="form-label">Текст выступления</label>
                    <textarea class="form-control @error('script') is-invalid @enderror"
                              id="script" name="script">{{ old('script') }}</textarea>
                    @error('script')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!-- Секция для аудио -->
                <div class="mb-4">
                    <h5>Аудио</h5>
                    <div class="mb-3">
                        <label for="audio_id" class="form-label">Выберите существующий аудиофайл</label>
                        <select class="form-control @error('audio_id') is-invalid @enderror"
                                id="audio_id" name="audio_id">
                            <option value="">Не выбрано</option>
                            @foreach($audios as $audio)
                                <option value="{{ $audio->id }}" {{ old('audio_id') == $audio->id ? 'selected' : '' }}>
                                    {{ $audio->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('audio_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="new_audio" class="form-label">Или загрузите новый аудиофайл</label>
                        <input type="file" class="form-control @error('new_audio') is-invalid @enderror"
                               id="new_audio" name="new_audio" accept=".mp3,.wav,.ogg">
                        @error('new_audio')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Поддерживаемые форматы: MP3, WAV, OGG. Максимальный размер: 10MB</small>
                    </div>
                </div>
                <!-- Секция для видео -->
                <div class="mb-4">
                    <h5>Видео</h5>
                    <div class="mb-3">
                        <label for="video_id" class="form-label">Выберите существующий видеофайл</label>
                        <select class="form-control @error('video_id') is-invalid @enderror"
                                id="video_id" name="video_id">
                            <option value="">Не выбрано</option>
                            @foreach($videos as $video)
                                <option value="{{ $video->id }}" {{ old('video_id') == $video->id ? 'selected' : '' }}>
                                    {{ $video->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('video_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="new_video" class="form-label">Или загрузите новый видеофайл</label>
                        <input type="file" class="form-control @error('new_video') is-invalid @enderror"
                               id="new_video" name="new_video" accept=".mp4,.avi,.mov">
                        @error('new_video')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Поддерживаемые форматы: MP4, AVI, MOV. Максимальный размер: 100MB</small>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Шутки</h5>
                    <div class="mb-3">
                        <select multiple class="form-control @error('jokes') is-invalid @enderror"
                                id="jokes" name="jokes[]">
                            @foreach($userJokes as $joke)
                                <option value="{{ $joke->id }}" {{ in_array($joke->id, old('jokes', [])) ? 'selected' : '' }}>
                                    {{ $joke->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('jokes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Зажмите Ctrl (Cmd на Mac) для выбора нескольких шуток</small>
                    </div>

                    <!-- Скрытые поля для порядка шуток будут добавляться через JavaScript -->
                    <div id="jokes-order-inputs"></div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('performances.index') }}" class="btn btn-secondary">Назад</a>
                    <button type="submit" class="btn btn-primary">Создать выступление</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Скрипт для управления порядком шуток
            document.getElementById('jokes').addEventListener('change', function() {
                const orderInputs = document.getElementById('jokes-order-inputs');
                orderInputs.innerHTML = '';

                const selectedOptions = Array.from(this.selectedOptions);
                selectedOptions.forEach((option, index) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `jokes_order[]`;
                    input.value = index;
                    orderInputs.appendChild(input);
                });
            });
        </script>

        <script>
            ClassicEditor
                .create(document.querySelector('#script'), {
                    toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
                })
                .catch(error => {
                    console.error(error);
                });
        </script>
    @endpush
@endsection
