@extends('layouts.app')

@section('title')
    Flujo contable
@endsection

@section('content')
    <div class="container">
        <h2 class="text-center">Transacciones</h2>
        <form action="{{ route('AccountingDetails') }}" method="GET" class="text-center mb-5">
            @if ($errors->any())
                <div class="alert alert-danger redondeado">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $error}}</strong>
                         </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @csrf
            <label for="startDate">Fecha de inicio:</label>
            <input type="date" name="startDate" id="startDate" value="{{!$errors->has('startDate') ? old('startDate', request('startDate')) : '' }}">

            <label for="endDate">Fecha de fin:</label>
            <input type="date" name="endDate" id="endDate" value="{{ !$errors->has('endDate') ? old('endDate', request('endDate')) : '' }}">

            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>

        @if(\Illuminate\Support\Facades\Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_MAYOR_CASH))
            <div class="text-center" style="overflow-x: auto;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Método de pago</th>
                        <th>Valor</th>
                        <th>Categoría</th>
                        <th>Fecha Registro</th>
                        <th>Usuario</th>
                        <th>Info</th>
                    </tr>
                    </thead>
                    <tbody name="table">
                        <tr>
                            <td><input type="number" id="id" name="id" placeholder="Id"></td>
                            <td>
                                <select id="payment_method">
                                    <option style="color: black" value="all" selected>Todos</option>
                                    @foreach ($paymentMethods as $paymentMethod)
                                        <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" id="amount" name="amount" placeholder="Valor"></td>
                            <td>
                                <select id="category">
                                    <option style="color: black" value="all" selected>Todas</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                    <option style="color: black" value="0">Otra</option>
                                </select>
                            </td>
                            <td><input type="date" name="filter_date" id="filter_date" placeholder="fecha"></td>
                            <td><input type="text" id="user" name="user" placeholder="user"></td>
                            <td><input type="text" id="data" name="data" placeholder="info"></td>
                        </tr>
                    @foreach ($transactions as $transaction)
                        <tr class="transaction-row">
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->payment->name }}</td>
                            <td class="currency">$ {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td>
                                <select onchange="onChangeCategory({{ $transaction->id }},this.value)" {{!Auth::user()->hasFeature(\App\Utils\FeaturesEnum::CHANGE_TRANSACTION_CATEGORY) ? 'disabled' : ''}}>
                                    <option style="color: black" value="" selected>Seleccione...</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{$transaction->category?->id == $category->id ? 'selected' : ''}}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{ $transaction->created_at }}</td>
                            <td>{{ $transaction->user->fullName }}</td>
                            <td>{{ substr($transaction->data, 0, 32) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Función para realizar la llamada AJAX genérica
        function performAjaxRequest(url, data) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            }).then(response => {
                if (response.ok) {
                    console.log('Operación exitosa');
                } else {
                    console.error('Error en la operación');
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        }

        function onChangeCategory(transactionId, categoryId) {
            performAjaxRequest("{{ route('transactions.category.update') }}", {
                transactionId: transactionId,
                categoryId: categoryId
            });
        }

        $(document).ready(function() {
            @if($categories)
                let options = @foreach ($categories as $category)
                    '<option value="{{$category->id}}" >{{ $category->name }}</option>' @if(!$loop->last)+@endif
                    @endforeach
            @endif

            function filter() {
                var idValue = $('#id').val();
                var paymentMethodValue = $('#payment_method').val();
                var amountValue = $('#amount').val();
                var categoryValue = $('#category').val();
                var dataValue = $('#data').val();
                var userValue = $('#user').val();
                var startDateValue = $('#startDate').val();
                var endDateValue = $('#endDate').val();
                var filterDateValue = $('#filter_date').val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/transactions/search',
                    method: 'GET',
                    data: {
                        id : idValue,
                        paymentMethod : paymentMethodValue,
                        amount : amountValue,
                        category : categoryValue,
                        data : dataValue,
                        user : userValue,
                        startDate : startDateValue,
                        endDate : endDateValue,
                        filterDate: filterDateValue,
                    },
                    dataType: 'json',
                    success: function (data) {
                        // Limpiar la tabla
                        $('tbody[name="table"] .transaction-row').remove();

                        data.forEach(function (result) {
                            $('tbody[name="table"]').append(

                                '<tr class="transaction-row" id=row_' + result.id + '>' +
                                '<td>' + result.id + '</td>' +
                                '<td>' + result.payment.name + '</td>' +
                                '<td>' + result.amount + '</td>' +
                                '<td>' +
                                '<select id="select_' + result.id + '" onchange="onChangeCategory(' + result.id + ', this.value)"' + '{{!Auth::user()->hasFeature(\App\Utils\FeaturesEnum::CHANGE_TRANSACTION_CATEGORY) ? "disabled" : ''}}' + '>' +
                                    '<option style="color: black" value="" selected>Seleccione...</option>' +
                                    options +
                                '</select>' +
                                '</td>' +
                                '<td>' + (result.created_at ? result.created_at.slice(0, 10) : '') + '</td>' +
                                '<td>' + result.user.nombre + ' ' + (result.user.apellido_1 ? result.user.apellido_1 : '') + ' ' + (result.user.apellido_2 ? result.user.apellido_2 : '') + '</td>' +
                                '<td>' + result.data.slice(0, 32) + '</td>' +
                                '</tr>'
                            );

                            if (result.category) {
                                $('#select_' + result.id).val(result.category.id);
                            }
                        });
                        $('.pagination').hide();
                    },
                    error: function (data) {
                        alert('Error filtering users');
                    }
                });
            }

            $('#id, #amount, #data, #user, #payment_method, #category, #filter_date').on('input', function () {
                filter();
            });
        });
    </script>
@endpush