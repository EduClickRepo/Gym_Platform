@extends('layouts.app')

@section('title')
    @lang('general.Plans')
@endsection

@push('head-content')
    <script
            type="text/javascript"
            src="https://checkout.wompi.co/widget.js"
    ></script>
    <style>
        .price-transition {
            transition: all 2s ease;
        }

        .hidden {
            opacity: 0;
            pointer-events: none;
        }

        .visible {
            opacity: 1;
        }
    </style>
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

@push('modals')
    <!-- Modal Contract-->
    <div class="modal m-auto" tabindex="-1" role="dialog" id="modalTerminos" style="height: 100vh; width: 75vw; z-index: 1051">
        <div role="document" class="m-auto h-100 w-100">
            <div class="modal-content h-100">
                <div class="modal-header h-100">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <object id="pdfViewer" type="application/pdf" frameborder="0" width="100%" height="100%" style="padding: 20px;">
                        <embed id="pdfEmbed" type='application/pdf' width="100%" height="100%" />
                    </object>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('.card');

            const pdfViewer = document.getElementById('pdfViewer');
            const pdfEmbed = document.getElementById('pdfEmbed');

            cards.forEach(card => {
                const select = card.querySelector('.payment-options');
                const originalPrice = card.querySelector('.price-original');
                const discountedPrice = card.querySelector('.price-discounted');

                const modalTrigger = card.querySelector('.view-contract'); // Botón de ver contrato
                const contractSingle = card.getAttribute('data-contract-single');
                const contractAutomatic = card.getAttribute('data-contract-automatic');

                function updateContract(pdfUrl) {
                    pdfViewer.setAttribute('data', pdfUrl);
                    pdfEmbed.setAttribute('src', pdfUrl);
                }

                if (modalTrigger) {
                    modalTrigger.addEventListener('click', function () {
                        updateContract(select.value === 'automatic' ? contractAutomatic : contractSingle);
                    });
                }

                if (!select || !originalPrice || !discountedPrice){
                    return;
                }

                // Manejar el cambio de selección
                select.addEventListener('change', function () {
                    if (select.value === 'automatic') {
                        // Mostrar precio reducido con transición
                        originalPrice.style.textDecoration = 'line-through';
                        originalPrice.style.fontSize = '16px';
                        originalPrice.classList.add('price-transition');

                        discountedPrice.classList.remove('hidden');
                        discountedPrice.classList.add('visible');
                        discountedPrice.style.fontSize = '24px';
                    } else if (select.value === 'single') {
                        // Mostrar solo el precio original con transición
                        originalPrice.style.textDecoration = 'none';
                        originalPrice.style.fontSize = '24px';
                        originalPrice.classList.add('price-transition');

                        discountedPrice.classList.add('hidden');
                        discountedPrice.classList.remove('visible');
                    }
                });

                // Configuración inicial
                if (select.value === 'automatic') {
                    originalPrice.style.textDecoration = 'line-through';
                    originalPrice.style.fontSize = '16px';
                    originalPrice.classList.add('price-transition');

                    discountedPrice.classList.remove('hidden');
                    discountedPrice.classList.add('visible');
                    discountedPrice.style.fontSize = '24px';
                }
            });
        });
    <!--PAYMENT-->

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

