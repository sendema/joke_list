@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Выберите тип подписки</div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Месячная подписка</h5>
                                        <p class="card-text">100 ₽/месяц</p>
                                        <button class="btn btn-primary" onclick="createPayment('monthly')">
                                            Выбрать
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Годовая подписка</h5>
                                        <p class="card-text">1000 ₽/год</p>
                                        <p class="text-success">Выгода 200 ₽!</p>
                                        <button class="btn btn-primary" onclick="createPayment('yearly')">
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
                    alert('Произошла ошибка');
                });
        }
    </script>
@endsection
