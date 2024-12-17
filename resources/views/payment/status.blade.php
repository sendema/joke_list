@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark">
                    <div class="card-header text-center text-danger">Статус платежа</div>

                    <div class="card-body" id="payment-status-container">
                        @if(session('info') && $payment && $payment->isPending())
                            <div class="alert alert-info text-center">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <h5 class="text-white">{{ session('info') }}</h5>
                                </div>
                            </div>
                        @endif

                        @if($payment)
                            <div class="payment-details">
                                <h5 class="text-center mb-4 text-white">Информация о платеже</h5>
                                <table class="table table-dark">
                                    <tr>
                                        <th>ID платежа:</th>
                                        <td>{{ $payment->payment_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Сумма:</th>
                                        <td>{{ $payment->formatted_amount }}</td>
                                    </tr>
                                    <tr>
                                        <th>Статус:</th>
                                        <td id="payment-status">
                                            @if($payment->isSuccessful())
                                                <span class="badge bg-success">Оплачено</span>
                                            @elseif($payment->isPending())
                                                <span class="badge bg-warning">В обработке</span>
                                            @elseif($payment->isCanceled())
                                                <span class="badge bg-danger">Отменен</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Дата:</th>
                                        <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    @if($payment->description)
                                        <tr>
                                            <th>Описание:</th>
                                            <td>{{ $payment->description }}</td>
                                        </tr>
                                    @endif
                                </table>

                                @if($payment->isSuccessful())
                                    <div class="alert alert-success text-center">
                                        <h5>Платёж успешно завершен!</h5>
                                        <p>Спасибо за оплату. Теперь у вас есть доступ к платформе!</p>
                                        <a href="{{ url('/jokes') }}" class="btn btn-danger btn-lg">
                                            Перейти к платформе
                                        </a>
                                    </div>
                                @elseif($payment->isPending())
                                    <div class="alert alert-warning text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <h5 class="text-white mb-3">Платёж обрабатывается</h5>
                                            <p class="text-white mb-4">Пожалуйста, подождите. Страница обновляется автоматически.</p>
                                            <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
                                                <span class="visually-hidden">Загрузка...</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning text-dark">
                                Информация о платеже не найдена
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($payment && $payment->isPending())
        @push('scripts')
            <script>
                function checkPaymentStatus() {
                    fetch(window.location.href)
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newContent = doc.getElementById('payment-status-container');
                            const currentContainer = document.getElementById('payment-status-container');
                            if (newContent && currentContainer) {
                                currentContainer.innerHTML = newContent.innerHTML;
                            }

                            // проверяем статус
                            const status = doc.querySelector('#payment-status .badge');
                            if (status && status.classList.contains('bg-warning')) {
                                setTimeout(checkPaymentStatus, 5000);
                            } else {
                                window.location.reload();
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }

                setTimeout(checkPaymentStatus, 5000);
            </script>
        @endpush
    @endif
@endsection
