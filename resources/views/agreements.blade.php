@extends('layouts.app')

@section('title')
    Agreements
@endsection

@push('head-content')
    <link rel="stylesheet" href="{{asset('css/agreements.css')}}">
@endpush

@section('content')
    <H2 class="text-center">Convenios</H2>
    <div class="m-auto col-10 col-md-6 text-center">
        <div id="agreementsCarousel" class="carousel slide mx-auto my-4" style="width: 200px" data-ride="carousel">
            <div class="carousel-inner w-100 h-100">
                @foreach($agreements as $agreement)
                    <div class="carousel-item @if($loop->first)active @endif">
                        <div style="width: fit-content" class="m-auto">
                            <img src="{{asset('images/agreements/'.$agreement->img)}}" class="d-block agreement-icon" alt="">
                            <p class="text-center mt-2">{{$agreement->name}}</p>
                            <p class="text-center">{{$agreement->discount}}*</p>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($agreements->count() > 1)
                <button class="carousel-control-prev" type="button" data-target="#agreementsCarousel" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-target="#agreementsCarousel" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </button>
            @endif
        </div>
        <h3>Generar QR</h3>
        <div class="form-group label-floating">
            <label class="control-label">Aliado</label>
            <select id="commerce" class="d-block mx-auto">
                <option style="color: black" value="" selected>Seleccione...</option>
                @foreach ($agreements as $agreement)
                    <option value="{{ $agreement->id }}">{{ $agreement->name }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn themed-btn mt-3" onclick="generateQR()">Generar</button>
        <div id="qr-container" class="mt-4" style="margin-bottom: 100px"></div>
        <p>*Aplican TyC</p>
    </div>
@endsection

@push('scripts')
    <script>
        function generateQR(){
            const commerceSelect = document.getElementById('commerce');
            const agreementId = commerceSelect.value;

            if (!agreementId) {
                alert('Por favor seleccione un aliado comercial.');
                return;
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('generate.qr') }}",
                method: "POST",
                data: {
                    agreement_id: agreementId
                },
                success: function (data){
                    document.getElementById('qr-container').innerHTML = data.qr;
                },
                error: function(data) {
                    console.log(data);

                    if (data.status === 403) {
                        alert('Debes tener un plan activo para aprovechar los convenios');
                    } else {
                        alert('Hubo un problema al generar el QR.');
                    }
                }
            });
        }
    </script>
@endpush
