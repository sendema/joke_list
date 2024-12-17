<?php

namespace App\Http\Controllers;

use App\Models\Joke;
use App\Models\Audio;
use App\Models\Video;
use App\Models\Performance;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    // отображение списка всех выступлений
    public function index(): View
    {
        $performances = Auth::user()->performances()
            ->with(['user', 'audio', 'video'])
            ->latest()
            ->paginate(10);

        return view('performances.index', compact('performances'));
    }

    // отображение формы для создания нового выступления
    public function create(): View
    {
        $audios = Audio::all();
        $videos = Video::all();
        $userJokes = Auth::user()->jokes;

        return view('performances.create', compact('audios', 'videos', 'userJokes'));
    }

    // сохранение нового выступления
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'script' => 'nullable|string',
            'date' => 'required|date',
            'jokes' => 'nullable|array',
            'jokes.*' => 'exists:jokes,id',
            'jokes_order' => 'nullable|array',
            'jokes_order.*' => 'integer',
            'audio_id' => 'nullable|exists:audios,id',
            'video_id' => 'nullable|exists:videos,id',
            'new_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:10240',
            'new_video' => 'nullable|file|mimes:mp4,avi,mov|max:102400',
        ]);

        // Обработка нового аудио
        if ($request->hasFile('new_audio')) {
            $audioPath = $request->file('new_audio')->store('audios', 'public');
            $audio = Audio::create([
                'name' => $validated['title'] . ' - Аудио',
                'path' => $audioPath,
            ]);
            $validated['audio_id'] = $audio->id;
        }

        // Обработка нового видео
        if ($request->hasFile('new_video')) {
            $videoPath = $request->file('new_video')->store('videos', 'public');
            $video = Video::create([
                'name' => $validated['title'] . ' - Видео',
                'path' => $videoPath,
            ]);
            $validated['video_id'] = $video->id;
        }

        // создаем выступление
        $performance = $request->user()->performances()->create($validated);

        // прикрепляем шутки
        if (!empty($validated['jokes'])) {
            $jokes = collect($validated['jokes'])->mapWithKeys(function ($jokeId, $index) use ($validated) {
                return [$jokeId => ['order' => $validated['jokes_order'][$index] ?? $index]];
            });
            $performance->jokes()->attach($jokes);
        }

        return redirect()->route('performances.index')
            ->with('success', 'Выступление успешно создано.');
    }

    // отображение выступления
    public function show(Performance $performance): View
    {
        $performance->load(['user', 'jokes', 'audio', 'video']);
        return view('performances.show', compact('performance'));
    }

    // отображение формы редактирования
    public function edit(Performance $performance): View
    {
        if (Auth::user()->id !== $performance->user_id) {
            abort(403);
        }

        $audios = Audio::all();
        $videos = Video::all();
        $userJokes = Auth::user()->jokes;

        return view('performances.edit', compact('performance', 'audios', 'videos', 'userJokes'));
    }

    // обновление выступления
    public function update(Request $request, Performance $performance): RedirectResponse
    {
//        dd([
//            'request_all' => $request->all(),
//            'user_id' => Auth::user()->id,
//            'performance_user_id' => $performance->user_id,
//            'has_audio' => $request->hasFile('new_audio'),
//            'has_video' => $request->hasFile('new_video'),
//            'jokes' => $request->input('jokes'),
//            'jokes_order' => $request->input('jokes_order'),
//        ]);
//
//        if (Auth::user()->id !== $performance->user_id) {
//            abort(403);
//        }
//
//        try {
//            $validated = $request->validate([
//                'title' => 'required|string|max:255',
//                'description' => 'nullable|string',
//                'script' => 'nullable|string',
//                'date' => 'required|date',
//                'jokes' => 'nullable|array',
//                'jokes.*' => 'exists:jokes,id',
//                'jokes_order' => 'nullable|array',
//                'jokes_order.*' => 'integer',
//                'audio_id' => 'nullable|exists:audios,id',
//                'video_id' => 'nullable|exists:videos,id',
//                'new_audio' => 'nullable|file|mimes:mp3,wav,ogg|max:10240',
//                'new_video' => 'nullable|file|mimes:mp4,avi,mov|max:102400',
//            ]);
//        } catch (\Illuminate\Validation\ValidationException $e) {
//            dd($e->errors()); // Покажет ошибки валидации
//        }

        if (Auth::user()->id !== $performance->user_id) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'script' => 'nullable|string',
            'date' => 'required|date',
            'jokes' => 'nullable|array',
            'jokes.*' => 'exists:jokes,id',
            'jokes_order' => 'nullable|array',
            'jokes_order.*' => 'integer',
            'audio_id' => 'nullable|exists:audios,id',
            'video_id' => 'nullable|exists:videos,id',
            'new_audio' => 'nullable|file|mimes:mp3,wav,ogg|max:10240',
            'new_video' => 'nullable|file|mimes:mp4,avi,mov|max:102400',
        ]);

        Log::info('Update method called', ['request' => $request->all()]);
        // Обработка нового аудио
        if ($request->hasFile('new_audio')) {
            // Удаляем старое аудио, если оно используется только в этом выступлении
            if ($performance->audio && $performance->audio->performances()->count() === 1) {
                Storage::disk('public')->delete($performance->audio->path);
                $performance->audio->delete();
            }

            $audioPath = $request->file('new_audio')->store('audios', 'public');
            $audio = Audio::create([
                'name' => $validated['title'] . ' - Аудио',
                'path' => $audioPath,
            ]);
            $validated['audio_id'] = $audio->id;
        }

        // Обработка нового видео
        if ($request->hasFile('new_video')) {
            // Удаляем старое видео, если оно используется только в этом выступлении
            if ($performance->video && $performance->video->performances()->count() === 1) {
                Storage::disk('public')->delete($performance->video->path);
                $performance->video->delete();
            }

            $videoPath = $request->file('new_video')->store('videos', 'public');
            $video = Video::create([
                'name' => $validated['title'] . ' - Видео',
                'path' => $videoPath,
            ]);
            $validated['video_id'] = $video->id;
        }

        // обновляем выступление
        $performance->update($validated);

        // обновляем шутки
        if (isset($validated['jokes'])) {
            $jokes = collect($validated['jokes'])->mapWithKeys(function ($jokeId, $index) use ($validated) {
                return [$jokeId => ['order' => $validated['jokes_order'][$index] ?? $index]];
            });
            $performance->jokes()->sync($jokes);
        } else {
            $performance->jokes()->detach();
        }
        return redirect()->route('performances.show', $performance)
            ->with('success', 'Выступление успешно обновлено.');
    }

    // удаление выступления
    public function destroy(Performance $performance): RedirectResponse
    {
        if (Auth::user()->id !== $performance->user_id) {
            abort(403);
        }

        // удаление файлов
        if ($performance->audio) {
            Storage::disk('public')->delete($performance->audio->path);
            $performance->audio->delete();
        }

        if ($performance->video) {
            Storage::disk('public')->delete($performance->video->path);
            $performance->video->delete();
        }

        $performance->delete();

        return redirect()->route('performances.index')
            ->with('success', 'Выступление успешно удалено.');
    }

    // добавление шутки к выступлению
    public function addJoke(Request $request, Performance $performance): RedirectResponse
    {
        if (Auth::user()->id !== $performance->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'joke_id' => 'required|exists:jokes,id',
            'order' => 'nullable|integer',
        ]);

        $performance->jokes()->attach($validated['joke_id'], [
            'order' => $validated['order'] ?? $performance->jokes()->count(),
        ]);

        return back()->with('success', 'Шутка добавлена в выступление.');
    }

    // удаление шутки из выступления
    public function removeJoke(Request $request, Performance $performance): RedirectResponse
    {
        if (Auth::user()->id !== $performance->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'joke_id' => 'required|exists:jokes,id',
        ]);

        $performance->jokes()->detach($validated['joke_id']);

        return back()->with('success', 'Шутка удалена из выступления.');
    }
}
