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
    <script type="text/javascript" src="https://checkout.epayco.co/checkout.js"></script>

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
                checkoutOptions.amountInCents = plan.price*100;//multiplicado por 100 por los centavos
                checkoutOptions.currency = currency;
                const timestamp = Date.now()
                checkoutOptions.reference = `GP-{{ \Illuminate\Support\Facades\Auth::id()}}-{{ \App\Utils\PayTypesEnum::Plan->value}}-${plan.id}-${timestamp}`;
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
                // TODO process unique payment
            });
        }
    </script>
    <!--END PAYMENT-->
@endpush

