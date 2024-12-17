<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Журнал шуток')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Основные стили */
        body {
            background: #1a1a1a;
            background-image: linear-gradient(rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.85)),
            url('/api/placeholder/1920/1080');
            background-size: cover;
            background-attachment: fixed;
            color: #fff;
            min-height: 100vh;
        }

        /* Навигация */
        .navbar {
            background: rgba(26, 26, 26, 0.95);
            box-shadow: 0 2px 20px rgba(255, 68, 68, 0.2);
            padding: 1rem 0;
            border-bottom: 2px solid #ff4444;
        }

        .navbar-brand {
            color: #ff4444 !important;
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-link {
            color: #fff !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #ff4444 !important;
        }

        /* Карточки и контент */
        .card {
            background: rgba(26, 26, 26, 0.95);
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(255, 68, 68, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 40px rgba(255, 68, 68, 0.3);
        }

        .card-header {
            background: rgba(255, 68, 68, 0.1);
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
            color: #ff4444;
            font-weight: bold;
        }

        .card-text {
            color: #fff !important;
        }

        .card .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* Кнопки */
        .btn-outline-danger {
            border-color: #ff4444;
            color: #ff4444;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            background-color: #ff4444;
            color: #fff;
            transform: translateY(-2px);
        }

        /* Формы */
        .form-control, .form-select {
            background: rgba(26, 26, 26, 0.9);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #fff;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(26, 26, 26, 0.95);
            border-color: #ff4444;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(255, 68, 68, 0.25);
        }

        /* Модальные окна */
        .modal-content {
            background: #1a1a1a !important;
            border: 1px solid #ff4444;
        }

        .modal-header {
            border-bottom-color: #ff4444 !important;
        }

        .modal-footer {
            border-top-color: #ff4444 !important;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Алерты */
        .alert {
            background: rgba(26, 26, 26, 0.9);
            border: 1px solid #ff4444;
            color: #fff;
        }

        /* Пагинация */
        .pagination {
            margin-top: 2rem;
        }

        .page-link {
            background: rgba(26, 26, 26, 0.9);
            border-color: #ff4444;
            color: #fff;
        }

        .page-link:hover, .page-item.active .page-link {
            background: #ff4444;
            border-color: #ff4444;
            color: #fff;
        }

        /* Эффекты */
        .spotlight {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            background: radial-gradient(
                circle at var(--x, 50%) var(--y, 50%),
                rgba(255, 68, 68, 0.1) 0%,
                rgba(255, 68, 68, 0) 20%
            );
            z-index: 0;
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

        /* Футер */
        .footer {
            background: rgba(26, 26, 26, 0.95);
            color: #fff;
            padding: 1.5rem 0;
            border-top: 2px solid #ff4444;
            margin-top: 3rem;
        }

        .logout-form {
            max-width: 100px;
            margin: 0 auto;
        }

        .dropdown-menu {
            background: rgba(26, 26, 26, 0.95);
            border: 1px solid #ff4444;
            min-width: 200px;
        }

        .dropdown-item {
            color: #fff;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
        }

        .dropdown-item:hover {
            background-color: rgba(255, 68, 68, 0.2);
            color: #ff4444;
        }

        .dropdown-divider {
            border-top: 1px solid rgba(255, 68, 68, 0.2);
        }

        .logout-form {
            width: 100%;
        }

        .dropdown-item.logout-btn {
            width: 100%;
            display: block;
            text-align: center;
        }

        .support-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(26, 26, 26, 0.9);
            border: 2px solid #ff4444;
            color: #ff4444;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.2);
        }

        .support-btn:hover {
            transform: scale(1.1);
            background: #ff4444;
            color: #fff;
            box-shadow: 0 6px 20px rgba(255, 68, 68, 0.4);
        }

        .support-btn i {
            font-size: 24px;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .support-btn.pulse {
            animation: pulse 2s infinite;
        }


    </style>
    @stack('styles')
</head>
<body>
<div class="spotlight"></div>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('jokes.index') }}">
            🎤 Joke list
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('jokes.index') }}">Шутки</a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jokes.create') }}">Добавить шутку</a>
                    </li>
                @endauth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('performances.index') }}">Выступления</a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('performances.create') }}">Добавить выступление</a>
                    </li>
                @endauth
            </ul>

            <ul class="navbar-nav">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Войти</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                    </li>
                @else
{{--                    <li class="nav-item dropdown">--}}
{{--                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">--}}
{{--                            {{ Auth::user()->name }}--}}
{{--                        </a>--}}
{{--                        <ul class="dropdown-menu dropdown-menu-end">--}}
{{--                            <li>--}}
{{--                                <form action="{{ route('logout') }}" method="POST" class="logout-form">--}}
{{--                                    @csrf--}}
{{--                                    <button type="submit" class="dropdown-item logout-btn">--}}
{{--                                        <i class="fas fa-sign-out-alt me-2"></i>Выйти--}}
{{--                                    </button>--}}
{{--                                </form>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </li>--}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
{{--                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Мой профиль</a></li>--}}
                            <li><a class="dropdown-item" href="{{ route('subscription.current') }}"><i class="fas fa-star me-2"></i>Подписка</a></li>
{{--                            <li><a class="dropdown-item" href="{{ route('settings') }}"><i class="fas fa-cog me-2"></i>Настройки</a></li>--}}
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item logout-btn">
                                        <i class="fas fa-sign-out-alt me-2"></i>Выйти
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-center">
            {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<footer class="footer">
    <div class="container text-center">
        <p>&copy; {{ date('Y') }} joke_list. Все права защищены.</p>
    </div>

        <!-- Кнопка техподдержки -->
        <button class="support-btn pulse" data-bs-toggle="modal" data-bs-target="#supportModal">
            <i class="fas fa-headset"></i>
        </button>

        <!-- Модальное окно техподдержки -->
        <div class="modal fade" id="supportModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-headset me-2"></i>Техническая поддержка
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="supportForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Имя</label>
                                <input type="text" class="form-control" name="name" required
                                       value="{{ Auth::user()->name ?? '' }}"
                                    {{ Auth::check() ? 'readonly' : '' }}>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required
                                       value="{{ Auth::user()->email ?? '' }}"
                                    {{ Auth::check() ? 'readonly' : '' }}>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Телефон</label>
                                <input type="tel" class="form-control" name="phone"
                                       placeholder="+7 (___) ___-__-__">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Приоритет</label>
                                <select class="form-select" name="priority" required>
                                    <option value="low">Низкий</option>
                                    <option value="normal" selected>Средний</option>
                                    <option value="high">Высокий</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Сообщение</label>
                                <textarea class="form-control" name="message" rows="4" required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-danger" id="submitSupport">
                            <span class="spinner-border spinner-border-sm d-none me-2"></span>
                            Отправить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.1.0/imask.min.js"></script>

<script>
    // Прожектор
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('mousemove', (e) => {
            const spotlight = document.querySelector('.spotlight');
            if (spotlight) {
                spotlight.style.setProperty('--x', `${e.clientX}px`);
                spotlight.style.setProperty('--y', `${e.clientY}px`);
            }
        });
    });
</script>
<!-- Скрипт техподдержки -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('supportForm');
        const submitBtn = document.getElementById('submitSupport');
        const spinner = submitBtn.querySelector('.spinner-border');
        const modal = new bootstrap.Modal(document.getElementById('supportModal'));

        // Маска для телефона
        const phoneInput = document.querySelector('input[name="phone"]');
        if (phoneInput) {
            IMask(phoneInput, {
                mask: '+{7} (000) 000-00-00'
            });
        }

        submitBtn.addEventListener('click', async function() {
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            submitBtn.disabled = true;
            spinner.classList.remove('d-none');

            try {
                const formData = new FormData(form);
                const response = await fetch('/api/tickets', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                const result = await response.json();

                if (response.ok) {
                    form.reset();
                    form.classList.remove('was-validated');
                    modal.hide();

                    const alert = `
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            Ваше обращение успешно отправлено!
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    document.querySelector('main').insertAdjacentHTML('afterbegin', alert);
                } else {
                    throw new Error(result.message || 'Произошла ошибка при отправке');
                }
            } catch (error) {
                const alert = `
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${error.message}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                `;
                document.querySelector('main').insertAdjacentHTML('afterbegin', alert);
            } finally {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
