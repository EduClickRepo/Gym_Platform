@extends('layouts.app')

@section('title')
    Flujo contable
@endsection

@section('content')
    <div class="container">
        <form action="{{ route('AccountingClose') }}" method="GET" class="text-center mb-5">
            @csrf
            <label for="startDate">Fecha de inicio:</label>
            <input type="date" name="startDate" id="startDate" value="{{old('startDate', request('startDate'))}}">

            <label for="endDate">Fecha de fin:</label>
            <input type="date" name="endDate" id="endDate" value="{{old('endDate', request('endDate'))}}">

            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>

        @if(\Illuminate\Support\Facades\Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_MAYOR_CASH))
            <div class="text-center">
                <h2>Medios de pago</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Método de pago</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactionsPerPaymentMethod as $transactionPerPaymentMethod)
                        <tr>
                            <td><a href="{{ route('AccountingDetails', [
                                    'startDate' => request('startDate'),
                                    'endDate' => request('endDate'),
                                    'paymentMethodId' => $transactionPerPaymentMethod->payment->id
                                ]) }}">{{ $transactionPerPaymentMethod->payment->name }}</td>
                            <td class="currency">$ {{ number_format($transactionPerPaymentMethod->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <h2>Categorias</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactionsPerCategoryMethod as $transactionPerCategoryMethod)
                        <tr>
                            <td><a href="{{ route('AccountingDetails', [
                                    'startDate' => request('startDate'),
                                    'endDate' => request('endDate'),
                                    'categoryId' => $transactionPerCategoryMethod->category ? $transactionPerCategoryMethod->category->id : 0
                                ]) }}">{{ $transactionPerCategoryMethod->category ? $transactionPerCategoryMethod->category->name : 'Indefinido' }} </a></td>
                            <td class="currency">$ {{ number_format($transactionPerCategoryMethod->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection