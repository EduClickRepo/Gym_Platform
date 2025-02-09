@extends('layouts.app')

@section('title')
    @lang('general.Plans')
@endsection

@push('head-content')
    <script
            type="text/javascript"
            src="https://checkout.wompi.co/widget.js"
    ></script>
@endpush

@section('content')
    <div class="d-md-flex justify-content-between justify-content-md-around w-75 m-auto flex-wrap">
        @foreach($plans as $plan)
            @if($plan->available_plans === null || $plan->available_plans > 0)
                @include('planCard')
            @endif
        @endforeach
    </div>
@endsection

@push('scripts')
    <!--PAYMENT-->
    <script>
        function createSubscription(token, amountInCents, currency, planId) {
            return new Promise((resolve, reject) => {
                $('#loading-spinner').show();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('paymentSubscription') }}",
                    method: "POST",
                    data: {
                        token: token,
                        amount: amountInCents,
                        currency: currency,
                        planId: planId
                    },
                    success: function (data) {
                        $('#loading-spinner').hide();
                        resolve(data);
                    },
                    error: function (error) {
                        $('#loading-spinner').hide();
                        reject(error);
                    }
                });
            });
        }

        async function callSignature(plan){
            return new Promise((resolve, reject) => {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('wompiSignature') }}",
                    method: "GET",
                    data: {
                        userId: {{\Illuminate\Support\Facades\Auth::id() ?? '0'}},
                        amount: plan.price*100,
                        currency: '{{\Illuminate\Support\Facades\Session::get('currency_id') ?? 'COP'}}',
                        planId: plan.id
                    },
                    success: function (data) {
                        resolve(data);
                    },
                    error: function (error) {
                        reject(error);
                    }
                });
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Delegar el evento de submit a los formularios dentro de un contenedor común
            document.body.addEventListener("submit", function (event) {
                const form = event.target; // Obtener el formulario que disparó el evento

                // Verificar si el formulario es uno de los formularios de pago
                if (form.matches("[id^=paymentForm]")) {
                    event.preventDefault(); // Prevenir el envío por defecto

                    @auth()
                        const planId = form.getAttribute("id").replace("paymentForm", ""); // Extraer ID del plan
                        const planData = @json($plans); // Asumiendo que $plans es el array de planes
                        const plan = planData.find(p => p.id == planId);

                        showPayModal(plan, form.querySelector(".payment-options"));
                    @else
                        window.location.href = "{{ route('login') }}";
                        return false;
                    @endauth
                }
            });
        });

        async function showPayModal(plan, selectElement) {
            const paymentOption = selectElement?.value ?? '';
            const currency = '{{\Illuminate\Support\Facades\Session::get('currency_id') ?? 'COP'}}';
            const checkoutOptions = {
                publicKey: '{{env('WOMPI_PUBLIC_KEY')}}',
            };

            if (paymentOption === 'automatic') {
                checkoutOptions.widgetOperation = 'tokenize';
                var amountInCents = plan.automatic_debt_price ?? 0;
            } else {
                const response = await callSignature(plan);
                checkoutOptions.widgetOperation = 'purchase';
                checkoutOptions.currency = currency;
                checkoutOptions.amountInCents = plan.price*100;//multiplicado por 100 por los centavos
                checkoutOptions.reference = response.reference;
                checkoutOptions.publicKey = '{{env('WOMPI_PUBLIC_KEY')}}';
                checkoutOptions.signature = { integrity: response.signature };
            }

            const checkout = new WidgetCheckout(checkoutOptions);

            checkout.open(async function (result) {
                if (paymentOption === 'automatic') {
                    var token = result.payment_source.token;

                    try {
                        const response = await createSubscription(token, amountInCents, currency, plan.id);
                        console.log("Subscription created:", response);
                        location.reload();
                    } catch (error) {
                        console.error("Error creating subscription:", error);
                    }
                }
            });
        }
    </script>
    <!--END PAYMENT-->
@endpush

