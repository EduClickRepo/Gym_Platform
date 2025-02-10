@extends('layouts.app')

@section('title')
    Flujo contable
@endsection

@section('content')
    <div class="container">
        <h1 class="text-center">Flujo de caja</h1>
        <form action="{{ route('AccountingFlow') }}" method="GET" class="text-center mb-5">
            @csrf
            <label for="start_date">Fecha de inicio:</label>
            <input type="date" name="start_date" id="start_date">

            <label for="end_date">Fecha de fin:</label>
            <input type="date" name="end_date" id="end_date">

            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>

        @if(\Illuminate\Support\Facades\Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_MAYOR_CASH))
            <div class="text-center">
                <h2>Ingresos</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Amount</th>
                        <th>Fecha</th>
                        <th>Metodo de pago</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($positiveValuesMayorCash as $positive)
                        <tr>
                            <td>{{ $positive->user?->id}}</td>
                            <td><a class="client-icon theme-color" href="{{route('visitarPerfil', ['user'=> $positive->user?->slug]) }}"><div style="max-height:3rem; overflow:hidden">{{$positive->user?->fullName}}</div></a></td>
                            <td class="currency">$ {{ number_format($positive->amount, 0, ',', '.') }}</td>
                            <td>{{ $positive->created_at->format('Y-m-d') }}</td>
                            <td>{{ $positive->payment->name }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2">Suma de ingresos</td>
                        <td class="currency">$ {{ number_format($positiveMayorCashSum, 0, ',', '.') }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center">
                <h2>Egresos</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Amount</th>
                        <th>Fecha</th>
                        <th>Metodo de pago</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($negativeValuesMayorCash as $negative)
                        <tr>
                            <td>{{ $negative->user?->id}}</td>
                            <td><a class="client-icon theme-color" href="{{route('visitarPerfil', ['user'=> $negative->user?->slug]) }}"><div style="max-height:3rem; overflow:hidden">{{$negative->user?->fullName}}</div></a></td>
                            <td class="currency">$ {{ number_format($negative->amount, 0, ',', '.') }}</td>
                            <td>{{ $negative->created_at->format('Y-m-d') }}</td>
                            <td>{{ $negative->payment->name }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2">Suma de egresos</td>
                        <td class="currency">$ {{ number_format($negativeMayorCashSum, 0, ',', '.') }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @if(\Illuminate\Support\Facades\Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_PETTY_CASH))
            <div class="text-center">
                <h2>Ingresos (Caja menor)</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Amount</th>
                        <th>Fecha</th>
                        <th>Metodo de pago</th>
                        <th>Item</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($positiveValuesPettyCash as $positive)
                        <tr>
                            <td>{{ $positive->user?->fullName }}</td>
                            <td class="currency">$ {{ number_format($positive->amount, 0, ',', '.') }}</td>
                            <td>{{ $positive->created_at->format('Y-m-d') }}</td>
                            <td>{{ $positive->payment->name }}</td>
                            <td>{{ $positive->data }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3">Suma de ingresos (caja menor)</td>
                        <td class="currency">$ {{ number_format($positivePettyCashSum, 0, ',', '.') }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center">
                <h2>Egresos (Caja menor)</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Amount</th>
                        <th>Fecha</th>
                        <th>Metodo de pago</th>
                        <th>Item</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($negativeValuesPettyCash as $negative)
                        <tr>
                            <td>{{ $negative->createdBy?->fullName }}</td>
                            <td class="currency">$ {{ number_format($negative->amount, 0, ',', '.') }}</td>
                            <td>{{ $negative->created_at->format('Y-m-d') }}</td>
                            <td>{{ $negative->payment->name }}</td>
                            <td>{{ $negative->data }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3">Suma de egresos (caja menor)</td>
                        <td class="currency">$ {{ number_format($negativePettyCashSum, 0, ',', '.') }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection