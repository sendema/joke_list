@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info mb-4">
                        {{ session('info') }}
                    </div>
                @endif

                <div class="card bg-dark">
                    <div class="card-header text-center text-danger">Выберите план подписки</div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Месячная подписка -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 bg-dark">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-white">Месячная подписка</h5>
                                        <div class="my-4">
                                            <h3 class="mb-0 text-white">100 ₽</h3>
                                            <small class="text-white">в месяц</small>
                                        </div>
                                        <ul class="list-unstyled mb-4 text-white">
                                            <li>✓ Полный доступ к функционалу</li>
                                            <li>✓ Обновления каждый день</li>
                                            <li class="invisible">.</li>
                                        </ul>
                                        <button class="btn btn-danger btn-lg w-100 mt-auto" onclick="createPayment('monthly')">
                                            Выбрать
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Годовая подписка -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 bg-dark border-light">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-white">Годовая подписка</h5>
                                        <div class="my-4">
                                            <h3 class="mb-0 text-white">1000 ₽</h3>
                                            <small class="text-white">в год</small>
                                            <div class="badge bg-danger mt-2">Экономия 200 ₽</div>
                                        </div>
                                        <ul class="list-unstyled mb-4 text-white">
                                            <li>✓ Полный доступ к функционалу</li>
                                            <li>✓ Обновления каждый день</li>
                                            <li>✓ Приоритетная поддержка</li>
                                        </ul>
                                        <button class="btn btn-danger btn-lg w-100 mt-auto" onclick="createPayment('yearly')">
                                            Выбрать
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function createPayment(type) {
                fetch('{{ route("payment.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        subscription_type: type
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.confirmation_url;
                        } else {
                            alert(data.message || 'Произошла ошибка');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Произошла ошибка при создании платежа');
                    });
            }
        </script>
    @endpush
@endsection
