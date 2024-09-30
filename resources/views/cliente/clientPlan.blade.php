<div class="py-3">
    <h3 class="mb-3">Mis planes:</h3>
    @if($clientPlans and $clientPlans->isNotEmpty())
        @foreach($clientPlans as $clientPlan)
            <div class="themed-block text-center text-md-left d-md-flex col-12 col-md-10 py-3 mx-auto mb-3">
                <div>
                    <h3 class="d-block my-2">{{$clientPlan->plan->name}}</h3>
                    @if($clientPlan->remaining_shared_classes)
                        <p class="d-block my-1"><strong>Clases Restantes: </strong>{{$clientPlan->remaining_shared_classes}}</p>
                    @endif
                    {{-- FIT-57: Uncomment this if you want specific classes--}}
                    @if($clientPlan->specificClasses && !$clientPlan->specificClasses->isEmpty())
                        @foreach($clientPlan->specificClasses as $class)
                            <p>{{$class->classType->type}}: {{$class->remaining_classes}}</p>
                        @endforeach
                    @endif

                    @if($clientPlan->unlimitedClasses && !$clientPlan->unlimitedClasses->isEmpty())
                        <p class="d-inline-block">@foreach($clientPlan->unlimitedClasses as $class)
                                {{$class->classType->type}}
                                @if(!$loop->last)
                                    <span>, </span>
                                @endif
                            @endforeach
                        </p>
                        <p class="d-inline-block" style="margin-left: -4px">: ∞</p>
                    @endif
                    {{--FIT-57: end block code--}}
                    @if($clientPlan->frozen_from)
                        <p class="d-block my-1"><strong>Cogelado: </strong>{{$clientPlan->frozen_from}} - {{$clientPlan->frozen_to}}</p>
                    @endif
                    <p class="d-block my-1"><strong>Válido hasta: </strong>{{$clientPlan->expiration_date}}</p>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center p-3 col-12 col-md-10 themed-block mx-auto mt-3">
            <p>Aún no tienes planes</p>
            <a href="{{route('plans')}}" class="">
                <button type="button" class="btn themed-btn mt-2">Ver planes</button>
            </a>
        </div>
    @endif
</div>
@if((Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_EXPIRED_PLANS) || Auth::user() == $user ) and $expiredPlans and $expiredPlans->isNotEmpty())
    <div class="p-3 themed-block col-12 col-md-10 mx-auto">
        <h3 class="mb-3">Planes Vencidos:</h3>
        <div class="table-responsive">
            <table class="w-100 table">
                <thead>
                <tr>
                    <th scope="col">Plan</th>
                    <th scope="col">F. Creación</th>
                    <th scope="col">F. Expiración</th>
                    <th scope="col">Clases Restantes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($expiredPlans as $expiredPlan)
                    <tr>
                        <td>{{$expiredPlan->name}}</td>
                        <td>{{$expiredPlan->created_at}}</td>
                        <td>{{$expiredPlan->expiration_date}}</td>
                        {{-- FIT-57: Load the remaining specific clases--}}
                        <td>{{$expiredPlan->remaining_shared_classes}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
