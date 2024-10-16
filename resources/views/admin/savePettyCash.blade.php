@extends('layouts.app')

@push('head-content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
          integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons"/>

    <link href="{{asset('css/profileWizard.css')}}" rel="stylesheet"/>

    <!--datetimePicker-->
    <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
    <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
    <script src="{{asset('js/datetimePicker.js')}}"></script>
@endpush

@section('content')

    <div class="container">
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

        <div class="themed-block mb-5 pb-4">
            <div class="wizard-container">
                <div class="wizard-card" data-color="purple" id="wizardProfile">
                    <form id="savePettyCashForm" method="post" action="{{route('pettyCash.save')}}" enctype="multipart/form-data">
                    @method('POST')
                    @csrf

                        <!--        You can switch " data-color="purple" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->
                        <div class="wizard-header">
                            <h3 class="wizard-title">
                                Registrar Transacción
                            </h3>
                        </div>
                        <div class="wizard-navigation">
                            <ul>
                                <li><a class="tab-completar-perfil" href="#payment" data-toggle="tab">Transacción</a></li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane" id="payment">
                                <br>
                                <div class="row mt-2">
                                    <div class="m-auto w-100">
                                        <div class="mb-3">
                                            <label for="transactionType" class="col-12 col-form-label text-md-center">Tipo transacción</label>
                                            <div class="col-12 col-md-8 m-auto">
                                                <div class="d-flex justify-content-around">
                                                    <div>
                                                        <input id="transactionType-income" class="{{ $errors->has('transactionType') ? ' is-invalid' : '' }}" name="transactionType" value="1" type="radio" autofocus>
                                                        <label for="transactionType-income">Ingreso</label>
                                                    </div>
                                                    <div>
                                                        <input id="transactionType-expense" class="{{ $errors->has('transactionType') ? ' is-invalid' : '' }}" name="transactionType" value="0" type="radio" autofocus>
                                                        <label for="transactionType-expense">Gasto</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group col-10 col-md-5 m-auto">
                                            <span class="iconos">
                                                <i class="fas fa-credit-card"></i>
                                            </span>
                                            <div class="form-group label-floating">
                                                <label class="control-label">Método de pago <small>(requerido)</small></label>
                                                <select class="form-control" id="paymentMethodId" name="paymentMethodId">
                                                    <option disabled selected value style="display:none"></option>
                                                    @foreach($paymentMethods as $paymentMethod)
                                                        <option class="color-black" value="{{$paymentMethod->id}}">{{$paymentMethod->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="input-group col-10 col-md-5 m-auto">
                                            <span class="iconos">
                                                <i class="material-icons">attach_money</i>
                                            </span>
                                            <div class="form-group label-floating">
                                                <label class="control-label">Valor <small>(requerido)</small></label>
                                                <input name="amount" type="number" step="1" class="form-control">
                                            </div>
                                        </div>
                                        <div class='input-group col-10 col-md-5 m-auto' id="datepicker">
                                            <span class="iconos">
                                                <i class="material-icons">calendar_today</i>
                                            </span>
                                            <div id="dateContainer" class="form-group label-floating">
                                                <label class="control-label">Día de pago <small>(requerido)</small></label>
                                                <input name="payDay" class="form-control input-group-addon" type="text">
                                            </div>
                                        </div>
                                        <div class="input-group col-10 col-md-5 m-auto">
                                            <span class="iconos">
                                                <i class="fas fa-credit-card"></i>
                                            </span>
                                            <div class="form-group label-floating">
                                                <label class="control-label">Categoría <small>(requerido)</small></label>
                                                <select class="form-control" id="categoryId" name="categoryId">
                                                    <option disabled selected value style="display:none"></option>
                                                    @foreach($categories as $category)
                                                        <option class="color-black" value="{{$category->id}}">{{$category->name}}</option>
                                                    @endforeach
                                                    <option class="color-black" value="0">Otra</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="data" class="input-group col-10 col-md-5 m-auto" style="display: none">
                                            <span class="iconos">
                                                <i class="fa fa-comments" aria-hidden="true"></i>
                                            </span>
                                            <div class="form-group label-floating">
                                                <label class="control-label">Item <small>(requerido)</small></label>
                                                <textarea name="data" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div id="person" class="input-group col-10 col-md-5 m-auto" style="display: none">
                                            <span class="iconos">
                                                <i class="material-icons">people</i>
                                            </span>
                                            <div class="form-group label-floating">
                                                <label class="control-label">Persona a la que se debe <small>(requerido)</small></label>
                                                <input name="person" class="form-control" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wizard-footer">
                            <div class="float-right">
                                <input type='button' class='btn btn-next btn-fill themed-btn btn-wd' name='next' id="next"
                                       value='Siguiente'/>
                                <input type='submit' class='btn btn-finish btn-fill themed-btn btn-wd' name='finish'
                                       value='Finalizar'/>
                            </div>
                            <div class="float-left">
                                <input type='button' class='btn btn-previous btn-fill btn-default btn-wd'
                                       name='previous' value='Atrás'/>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- Remove account payable when the transaction is an income and show/hide the person to whom it is owed-->
    <script>
        const selectPaymentMethod = document.getElementById('paymentMethodId');
        const selectCategory = document.getElementById('categoryId');
        document.addEventListener('DOMContentLoaded', function() {

            selectPaymentMethod.addEventListener('change', function () {
                let selectedText = selectPaymentMethod.options[selectPaymentMethod.selectedIndex].text
                if (selectedText === '{{\App\Utils\PaymentMethodsEnum::ACCOUNT_PAYABLE->value}}') {
                    $("#person").show();
                } else {
                    $("#person").hide();
                }
            });

            selectCategory.addEventListener('change', function () {
                let selectedText = selectCategory.options[selectCategory.selectedIndex].text
                if (selectedText === '{{\App\Utils\CategoriesEnum::OTRA->value}}') {
                    $("#data").show();
                } else {
                    $("#data").hide();
                }
            });

            $('input[name="transactionType"]').change(function() {
                selectPaymentMethod.value = "";
                selectPaymentMethod.dispatchEvent(new Event('change'));
                var selectedValue = $('input[name="transactionType"]:checked').val();
                if (selectedValue === "1") {
                    $('#paymentMethodId option').each(function() {
                        var paymentMethodType = $(this).text();
                        if (paymentMethodType === '{{\App\Utils\PaymentMethodsEnum::ACCOUNT_PAYABLE->value}}') {
                            $(this).hide();
                        }
                    });
                } else {
                    $('#paymentMethodId option').show();
                }
            });
        });
    </script>

    <!--datetimePicker configuration-->
    <script>
        $(function () {
            var actualDate = new Date();
            actualDate.setHours(23,59);
            $('#datepicker').datetimepicker({
                ignoreReadonly: true,
                format: 'DD/MM/YYYY',
                maxDate: actualDate,
                locale: 'es',
                useCurrent: false //Para que con el max date no quede seleccionada por defecto esa fecha
            });
            $("#datepicker").on("dp.change", function (e) {
                if(e.date == ''){
                    $("#dateContainer").addClass( "is-empty" );
                }else{
                    $("#dateContainer").removeClass( "is-empty" );
                }
            });
        });
    </script>

    <!--Wizard -->
    <script src="{{asset('js/jquery.bootstrap.js')}}"></script>
    <script src="{{asset('js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('js/validate-savePettyCash.js')}}"></script>
    <script src="{{asset('js/wizard.js')}}"></script>
@endpush
