@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>История платежей</h2>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Сумма</th>
                    <th>Описание</th>
                    <th>Статус</th>
                    <th>Дата</th>
                </tr>
                </thead>
                <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_id }}</td>
                        <td>{{ $payment->formatted_amount }}</td>
                        <td>{{ $payment->description }}</td>
                        <td>
                            <span class="badge bg-{{ $payment->isSuccessful() ? 'success' : ($payment->isPending() ? 'warning' : 'danger') }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{ $payments->links() }}
    </div>
@endsection
