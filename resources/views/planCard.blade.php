<div style="width: 270px;" class="card themed-block text-center py-4 px-1 mb-5 mx-auto d-flex flex-column align-items-center @if($plan->highlighted) highlighted-plan @endif" style="height: 75vh">
    <div class="mx-auto mb-auto">
        <h2>Membresía</h2>
        <h2>{{$plan->name}}</h2>
        <div style="height: 160px" class="d-flex my-3">
            <img height="100%" src="{{asset("images/".$plan->image)}}" class="m-auto" />
        </div>
    </div>
    <form id="paymentForm{{$plan->id}}" >
        <div class="mx-auto w-75">
            <div class="form-group">
            @if($plan->automatic_debt_price)
                    <select name="paymentOptions" class="form-control payment-options">
                        <option value="automatic" selected>Débito Automático</option>
                        <option value="single">Pago Único</option>
                    </select>

                    <div class="mt-3">
                        <h2 class="price-display">
                            <span class="text-muted price-original price-transition" style="text-decoration: line-through; font-size: 16px;">
                                ${{ number_format($plan->price, 0, '.', ',') }}
                            </span>
                            <br>
                            <span class="text-primary price-discounted price-transition" style="font-size: 24px;">
                                ${{ number_format($plan->automatic_debt_price, 0, '.', ',') }}
                            </span>
                        </h2>
                    </div>
                @else
                    <h2><strong>${{ number_format($plan->price, 0, '.', ',') }}</strong></h2>
                @endif
            </div>

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

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const cards = document.querySelectorAll('.card');

                    cards.forEach(card => {
                        const select = card.querySelector('.payment-options');
                        const originalPrice = card.querySelector('.price-original');
                        const discountedPrice = card.querySelector('.price-discounted');

                        if (!select || !originalPrice || !discountedPrice) return;

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
            </script>

            @if($plan->unlimited)
                <p>Sesiones ilimitadas</p>
            @else
                <p>{{$plan->number_of_shared_classes }} sesiones</p>
            @endif
            {{--FIT-57: Uncomment this if you want specific classes
            @isset($plan->sharedClasses)
                <table class="table m-0">
                    <tbody>
                        @foreach($plan->sharedClasses as $class)
                        <tr @if($loop->first)class="border-top"@endif>
                            <td class="border-top-0 text-left align-middle">{{$class->classType->type}}</td>
                            @if($loop->first)
                                <td class="border-top-0 text-right align-middle" rowspan="{{$loop->count}}">{{$plan->number_of_shared_classes}}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endisset
            @isset($plan->specificClasses)
                <table class="table m-0">
                    <tbody>
                        @foreach($plan->specificClasses as $class)
                            <tr @if($loop->first)class="border-top"@endif>
                                <td class="border-top-0 text-left align-middle">{{$class->classType->type}}</td>
                                <td class="border-top-0 text-right align-middle">{{$class->number_of_classes}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endisset
            @isset($plan->unlimitedClasses)
                <table class="table m-0">
                    <tbody>
                        @foreach($plan->unlimitedClasses as $class)
                            <tr @if($loop->first)class="border-top"@endif>
                                <td class="border-top-0 text-left align-middle">{{$class->classType->type}}</td>
                                @if($loop->first)
                                    <td class="border-top-0 text-right align-middle" style="font-size: xx-large; line-height: 0.8" rowspan="{{$loop->count}}">∞</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endisset
            --}}
            @isset($plan->benefits)
                <table class="table m-0">
                    <tbody>
                    @foreach($plan->benefits as $benefit)
                        <tr @if($loop->first)class="border-top"@endif>
                            <td class="border-top-0 text-left pr-0 align-middle" style="width: 90%">{{$benefit->benefit}}</td>
                            <td class="border-top-0 text-right align-middle pl-0"><i class="fas fa-check"></i>                       </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endisset
            <table class="table m-0">
                <tbody>
                <tr class="border-top">
                    <td class="border-top-0 text-left align-middle">Duración</td>
                    <td class="border-top-0 text-right align-middle" style="width: 90%">{{$plan->duration}} {{$plan->duration_type }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        @auth()
            <div class="form-check m-auto">
                <input class="form-check-input" type="checkbox" name="aceptacion" id="aceptacion" required>
                <label class="form-check-label terms-label" for="aceptacion">
                    <small>Acepto el
                        <a style="text-decoration: none" href="javascript:void(0);" data-toggle="modal" data-target="#modalTerminos">
                            <b><u>Contrato de Servicio</u></b>
                        </a>
                    </small>
                </label>
            </div>
        @endauth

        <button type="submit" class="btn color-white themed-btn mt-3 position-absolute" style="left: 50%; transform: translateX(-50%);">
            Seleccionar
        </button>
    </form>
</div>

@push('modals')
    <!-- Modal terminos y condiciones-->
    <div class="modal m-auto" tabindex="-1" role="dialog" id="modalTerminos" style="height: 100vh; width: 75vw; z-index: 1051">
        <div role="document" class="m-auto h-100 w-100">
            <div class="modal-content h-100">
                <div class="modal-header h-100">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <object data="{{asset('pdf/Terminos_y_condiciones_V2-GRL_PWR.pdf')}}" type="application/pdf" frameborder="0" width="100%" height="100%" style="padding: 20px;">
                        <embed src="{{asset('pdf/Terminos_y_condiciones_V2-GRL_PWR.pdf')}}" type='application/pdf' width="100%" height="100%" />
                    </object>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal m-auto" tabindex="-1" role="dialog" id="modalConsentimiento" style="z-index: 1051!important; height: 100vh; width: 75vw;">
        <div role="document" class="m-auto h-100 w-100">
            <div class="modal-content h-100">
                <div class="modal-header h-100">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <object data="{{asset('pdf/Consentimiento_InformadoV2-GRL_PWR.pdf')}}" type="application/pdf" frameborder="0" width="100%" height="100%" style="padding: 20px;">
                        <embed src="{{asset('pdf/Consentimiento_InformadoV2-GRL_PWR.pdf')}}" type='application/pdf' width="100%" height="100%" />
                    </object>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endpush