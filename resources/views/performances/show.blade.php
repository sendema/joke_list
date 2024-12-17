<!-- resources/views/performances/show.blade.php -->
@extends('layouts.app')

@section('title', $performance->title)

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $performance->title }}</h4>
            @if(auth()->id() === $performance->user_id)
                <div>
                    <a href="{{ route('performances.edit', $performance) }}" class="btn btn-primary">Редактировать</a>
                    <form action="{{ route('performances.destroy', $performance) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить выступление?')">Удалить</button>
                    </form>
                </div>
            @endif
        </div>
        <div class="card-body">
            <div class="mb-4">
                <p class="mb-1"><strong>Дата выступления:</strong> {{ $performance->date->format('d.m.Y') }}</p>
                <p class="mb-1"><strong>Автор:</strong> {{ $performance->user->name }}</p>
                @if($performance->description)
                    <p class="mt-3">{{ $performance->description }}</p>
                @endif
            </div>

            @if($performance->script)
                <div class="mb-4">
                    <h5>Текст выступления:</h5>
                    <div class="mt-3">
                        {!! $performance->script !!}
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <h5>Программа выступления:</h5>
                @if($performance->jokes->isEmpty())
                    <p class="text-muted">В программе пока нет шуток</p>
                @else
                    <div class="list-group mt-3">
                        @foreach($performance->jokes->sortBy('pivot.order') as $joke)
                            <div class="list-group-item bg-dark text-white border-danger">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $joke->title }}</h6>
                                        <small>{{ Str::limit($joke->description, 100) }}</small>
                                    </div>
                                    @if(auth()->id() === $performance->user_id)
                                        <form action="{{ route('performances.jokes.remove', ['performance' => $performance, 'joke' => $joke]) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(auth()->id() === $performance->user_id)
                    <div class="mt-4">
                        <form action="{{ route('performances.jokes.add', $performance) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <select name="joke_id" class="form-control bg-dark text-white border-danger">
                                    <option value="">Выберите шутку</option>
                                    @foreach(Auth::user()->jokes as $joke)
                                        @if(!$performance->jokes->contains($joke->id))
                                            <option value="{{ $joke->id }}">{{ $joke->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">Добавить шутку</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('performances.index') }}" class="btn btn-secondary">Назад к списку</a>
    </div>
@endsection
