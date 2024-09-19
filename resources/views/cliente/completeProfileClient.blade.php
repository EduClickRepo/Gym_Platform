@section('tab3Title')
    Información físca
@endsection

@section('tab3Content')
    <div class="row m-auto">
        <div class="col-sm-12 col-md-6 m-auto">
            <div class="input-group">
                <span class="input-group-addon iconos">
                    <span class="fas fa-weight"></span>
                </span>
                <div class="form-group label-floating">
                    <label class="control-label">Peso aproximado <small>(kilogramos)</small></label>
                    @if($user->cliente?->peso())
                        <input name="peso" type="number" step="1" class="form-control" required
                               value="{{number_format($user->cliente->peso()->peso, 2)}}">
                    @else
                        <input name="peso" type="number" step="1" class="form-control" required>
                    @endif
                </div>
            </div>

            <div class="input-group">
                <span class="input-group-addon iconos">
                    <i class="fas fa-ruler-horizontal"></i>
                </span>
                <div class="form-group label-floating">
                    <label class="control-label">Estatura inicial <small>(centimetros)</small></label>
                    @if($user->cliente?->estatura())
                        <input name="estatura" type="number" step="1" class="form-control" required
                               value="{{number_format($user->cliente->estatura()->estatura, 2)}}">
                    @else
                        <input name="estatura" type="number" step="1" class="form-control" required>
                    @endif
                </div>
            </div>
            <div class="input-group">
                <span class="input-group-addon iconos">
                    <i class="fas fa-shoe-prints"></i>
                </span>
                <div class="form-group label-floating">
                    <label class="control-label">Talla de zapatos <small>(34 a 45)</small></label>
                    @if($user->cliente?->talla_zapato)
                        <input name="tallaZapato" type="number" step="1" min="34" class="form-control" required
                               value="{{number_format($user->cliente->talla_zapato, 2)}}">
                    @else
                        <input name="tallaZapato" type="number" step="1" class="form-control" required>
                    @endif
                </div>
            </div>
            <!--
            <div class="input-group">
                <span class="input-group-addon iconos">
                    <span class="fas fa-weight"></span>
                </span>
                <div class="form-group label-floating">
                    <label class="control-label">Meta <small>(kilogramos)</small></label>
                    @if(Auth::user()->cliente != null)
                        <input name="pesoIdeal" type="number" step="any" class="form-control" required
                               value="{{number_format(Auth::user()->cliente->peso_ideal, 2)}}">
                    @else
                        <input name="pesoIdeal" type="number" step="any" class="form-control" required>
                    @endif
                </div>
            </div>

            <div class="input-group">
                <span class="iconos" style="padding: 0/*para que en firefox queden horizontales*/">
                    <i class="fas fa-venus-mars"></i>
                </span>
                <div class="radio">
                    <label class="radio-label">
                        <input required type="radio" name="genero"
                               value="f" {{Auth::user()->genero != null && Auth::user()->genero == 'f' ? "checked=true" : ""}}>
                        Femenino
                    </label>
                </div>
                <div class="radio">
                    <label class="radio-label">
                        <input required type="radio" name="genero"
                               value="m" {{Auth::user()->genero != null && Auth::user()->genero == 'm' ? "checked=true" : ""}}>
                        Masculino
                    </label>
                </div>
            </div>
            -->

            <div class="input-group">
                <span class="input-group-addon iconos">
                    <i class="fa-solid fa-bullseye"></i>
                </span>
                <div class="form-group label-floating">
                    <label class="control-label">¿Cúal es tu objetivo? <small>(requerido)</small></label>
                    <input name="objective" id="objective" class="form-control" value="{{$user->cliente?->objective}}">
                </div>
            </div>

            @include('components.requestPathology')
        </div>
    </div>
@endsection
