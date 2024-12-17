<?php

use App\Http\Controllers\JokeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\LoginController;

Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegistrationController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Это одна строка
//Route::resource('jokes', JokeController::class);
// Эквивалентна следующим маршрутам:
Route::get('/jokes', [JokeController::class, 'index'])->name('jokes.index');// Список всех шуток пользователя
Route::get('/jokes/create', [JokeController::class, 'create'])->name('jokes.create');// Форма создания шутки
Route::post('/jokes', [JokeController::class, 'store'])->name('jokes.store');// Сохранение новой шутки
Route::get('/jokes/{joke}', [JokeController::class, 'show'])->name('jokes.show');// Просмотр конкретной шутки
Route::get('/jokes/{joke}/edit', [JokeController::class, 'edit'])->name('jokes.edit');// Форма редактирования шутки
Route::put('/jokes/{joke}', [JokeController::class, 'update'])->name('jokes.update');// Обновление шутки
Route::delete('/jokes/{joke}', [JokeController::class, 'destroy'])->name('jokes.destroy');// Удаление шутки

Route::get('/performances', [PerformanceController::class, 'index'])->name('performances.index');// Отображение списка всех выступлений
Route::get('/jokes/filter', [JokeController::class, 'filter'])->name('jokes.filter');// Фильтрация шуток
Route::get('/performances/create', [PerformanceController::class, 'create'])->name('performances.create');// Форма создания нового выступления
Route::post('/performances', [PerformanceController::class, 'store'])->name('performances.store');// Сохранение нового выступления
Route::get('/performances/{performance}', [PerformanceController::class, 'show'])->name('performances.show');// Отображение конкретного выступления
Route::get('/performances/{performance}/edit', [PerformanceController::class, 'edit'])->name('performances.edit');// Форма редактирования выступления
Route::put('/performances/{performance}', [PerformanceController::class, 'update'])->name('performances.update');// Обновление выступления
Route::delete('/performances/{performance}', [PerformanceController::class, 'destroy'])->name('performances.destroy');// Удаление выступления
Route::post('/performances/{performance}/jokes', [PerformanceController::class, 'addJoke'])->name('performances.jokes.add');// Добавление шутки к выступлению
Route::delete('/performances/{performance}/jokes/{joke}', [PerformanceController::class, 'removeJoke'])->name('performances.jokes.remove');// Удаление шутки из выступления


Route::middleware(['auth'])->group(function () {
    // Маршруты для подписок
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'showSubscriptionForm'])->name('form');
        Route::get('/current', [SubscriptionController::class, 'current'])->name('current');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/renew', [SubscriptionController::class, 'renew'])->name('renew');
    });

    // Маршруты для платежей
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::post('/create', [PaymentController::class, 'create'])->name('create');
        Route::get('/callback', [PaymentController::class, 'callback'])->name('callback');
        Route::post('/webhook', [PaymentController::class, 'webhook'])->name('webhook')->withoutMiddleware(['auth']);
        Route::get('/status/{payment?}', [PaymentController::class, 'status'])->name('status'); // Добавьте эту строку
    });
});

Route::post('/api/tickets', [TicketController::class, 'store']);

