@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">Информация о подписке</div>

                    <div class="card-body">
                        @if($subscription)
                            <div class="mb-4">
                                <h5>Текущий план:</h5>
                                <p class="lead">
                                    {{ $subscription->type === 'monthly' ? 'месячная подписка' : 'годовая подписка' }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <h5>Статус:</h5>
                                @if($subscription->isActive())
                                    <span class="badge bg-success">активна</span>
                                @else
                                    <span class="badge bg-danger">неактивна</span>
                                @endif
                            </div>

                            <div class="mb-4">
                                <h5>Дата окончания:</h5>
{{--                                <p>{{ $subscription->expires_at->format('d.m.Y H:i') }}</p>--}}
                                <p>{{ \Carbon\Carbon::parse($subscription->expires_at)->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="text-center mb-3">
                            @if($subscription->isActive())
                                <form action="{{ route('subscription.renew') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="subscription_type" value="{{ $subscription->type }}">
                                    <button type="submit" class="btn btn-success">Продлить подписку</button>
                                </form>
                                <form action="{{ route('subscription.cancel') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Вы уверены, что хотите отменить подписку?')">
                                        Отменить подписку
                                    </button>
                                </form>
                            </div>
                            @else
                                <a href="{{ route('subscription.form') }}" class="btn btn-primary">
                                    Оформить новую подписку
                                </a>
                            @endif
                        @else
                            <div class="text-center">
                                <h4>У вас нет активной подписки</h4>
                                <p>Оформите подписку для доступа к контенту</p>
                                <a href="{{ route('subscription.form') }}" class="btn btn-primary btn-lg">
                                    Оформить подписку
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- История платежей -->
                <div class="card mt-4">
                    <div class="card-header text-center">История платежей</div>
                    <div class="card-body">
                        @if(auth()->user()->payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Описание</th>
                                        <th>Сумма</th>
                                        <th>Статус</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach(auth()->user()->payments()->latest()->get() as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                                            <td>{{ $payment->description }}</td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>
                                                @if($payment->isSuccessful())
                                                    <span class="badge bg-success">Оплачен</span>
                                                @elseif($payment->isPending())
                                                    <span class="badge bg-warning">В обработке</span>
                                                @else
                                                    <span class="badge bg-danger">Отменен</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">История платежей пуста</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<style>
    .card-body h5 {
        color: white;
    }

    .card-body p,
    .card-body .lead {
        color: white;
    }

    .card-body .badge {
        font-size: 1rem;
    }
</style>
