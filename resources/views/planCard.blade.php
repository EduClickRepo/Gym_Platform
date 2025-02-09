<div style="width: 270px;" class="card themed-block text-center py-4 px-1 mb-5 mx-auto d-flex flex-column align-items-center @if($plan->highlighted) highlighted-plan @endif" style="height: 75vh"
     data-contract-single="{{ asset('pdf/contracts/' . $plan->contract) }}"
     data-contract-automatic="{{ asset('pdf/contracts/' . $plan->contract_automatic_debt) }}">
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
                        <a class="view-contract" style="text-decoration: none" href="javascript:void(0);" data-toggle="modal" data-target="#modalTerminos">
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

