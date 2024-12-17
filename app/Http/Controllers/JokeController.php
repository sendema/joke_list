<?php

namespace App\Http\Controllers;

use App\Models\Joke;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JokeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // получаем все шутки текущего пользователя с категориями
        $query = Joke::with(['user', 'category'])
            ->where('user_id', Auth::id());

        // поиск по названию (регистронезависимый)
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
        }

        // фильтрация по категории
        if ($request->has('category')) {
            $query->where('id_category', $request->category);
        }

        // фильтрация по рейтингу
        if ($request->filled('rating')) {
            $rating = floatval($request->rating);
            $query->where('rating', '>=', $rating);
        }

        // сортировка
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest('created_at');
                    break;
                case 'rating':
                    $query->orderByDesc('rating');
                    break;
                default:
                    $query->latest('created_at');
                    break;
            }
        } else {
            $query->latest('created_at'); // по умолчанию сортируем по дате (сначала новые)
        }
        $jokes = $query->paginate(10)
            ->withQueryString();

        // если поиск не вернул результатов
        if ($request->has('search') && $jokes->isEmpty()) {
            session()->flash('info', 'По запросу "' . $request->search . '" шутки не найдены');
        }

        // получаем все категории для фильтра
        $categories = Category::all();

        // создаем массив рейтингов для фильтра
        $ratings = [
            1 => '⭐ и выше',
            2 => '⭐⭐ и выше',
            3 => '⭐⭐⭐ и выше',
            4 => '⭐⭐⭐⭐ и выше',
            5 => '⭐⭐⭐⭐⭐'
        ];

        return view('jokes.index', compact('jokes', 'categories', 'ratings'));
    }

    // показать форму для создания новой шутки
    public function create()
    {
        $categories = Category::all();
        return view('jokes.create', compact('categories'));
    }

    // сохранить новую шутку
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'id_category' => 'required|exists:categories,id',
            'rating' => 'required|numeric|between:0,5'
        ]);

        $joke = Auth::user()->jokes()->create($validated);

        return redirect()->route('jokes.show', $joke)
            ->with('success', 'Шутка успешно создана!');
    }

    // показать конкретную шутку
    public function show(Joke $joke)
    {
        return view('jokes.show', compact('joke'));
    }

    // показать форму для редактирования шутки
    public function edit(Joke $joke)
    {
        // проверяем, является ли пользователь автором шутки
        if ($joke->user_id !== Auth::id()) {
            return redirect()->route('jokes.index')
                ->with('error', 'У вас нет прав для редактирования этой шутки');
        }

        $categories = Category::all();
        return view('jokes.edit', compact('joke', 'categories'));
    }

    // обновить шутку
    public function update(Request $request, Joke $joke)
    {
        // проверяем права доступа
        if ($joke->user_id !== Auth::id()) {
            return redirect()->route('jokes.index')
                ->with('error', 'У вас нет прав для редактирования этой шутки');
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'id_category' => 'required|exists:categories,id',
            'rating' => 'required|numeric|between:0,5'
        ]);

        $joke->update($validated);

        return redirect()->route('jokes.show', $joke)
            ->with('success', 'Шутка успешно обновлена!');
    }

    // удалить шутку
    public function destroy(Joke $joke)
    {
        // проверяем права доступа
        if ($joke->user_id !== Auth::id()) {
            return redirect()->route('jokes.index')
                ->with('error', 'У вас нет прав для удаления этой шутки');
        }

        $joke->delete();

        return redirect()->route('jokes.index')
            ->with('success', 'Шутка успешно удалена!');
    }
}
