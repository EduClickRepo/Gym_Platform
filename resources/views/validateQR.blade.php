@extends('layouts.app')

@section('title')
    Convenio
@endsection


@section('content')
    <div class="text-center">
        <H2>Convenios</H2>
        @switch($status)
            @case(401)<!--Token expirado-->
                <p>El QR ha expirado, pídele al cliente que lo genere nuevamente</p>
                @break
            @case(403)<!--firma invalida-->
                <p>El QR no es válido, dile al cliente que se comunique con el soporte de Girl Power</p>
                @break
            @case(200)
                <p>Ha llegado <strong>{{$user->fullname}}</strong></p>
                <p>dale la mejor atención y sigamos creciendo Juntas</p>
                @break
        @endswitch
    </div>
@endsection
