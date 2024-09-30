@extends('layouts.app')
@section('title')
    Users
@endsection
@section('content')
    <div class="container" style="overflow-x: scroll;">
        <div class="d-flex">
            <h2>Listado de Usuarios</h2>
            <div class="ml-auto my-auto">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="expirationType1" name="expirationType" value="all" checked=checked class="custom-control-input">
                    <label class="custom-control-label" for="expirationType1">Todos</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="expirationType2" name="expirationType" value="active" class="custom-control-input">
                    <label id="expirationType2Label" class="custom-control-label" for="expirationType2">Activos</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="expirationType3" name="expirationType" value="inactive" class="custom-control-input">
                    <label class="custom-control-label" for="expirationType3">Inactivos</label>
                </div>
            </div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Madrina</th>
                <th>Acciones</th>
                <th>Estado de valoracion</th>
                <th>Tiene foto?</th>
                <th>Pertenece al grupo de WA?</th>
            </tr>
            </thead>
            <tbody name="table">
                <tr>
                    <td><input type="number" id="id" name="id" placeholder="Id" ></td>
                    <td><input type="text" id="name" name="name" placeholder="Nombre"></td>
                    <td><input type="text" id="email" name="email" placeholder="Correo"></td>
                    <td><input type="number" id="phone" name="phone" placeholder="Celular"></td>
                    <td><input type="number" id="assigned" name="assigned" placeholder="assigned"></td>
                    <td>F. Expiración</td>
                    <td>
                        <div class="form-check m-auto">
                            <input class="form-check-input" type="checkbox" name="needAssessment" id="needAssessment">
                            <label class="form-check-label terms-label" for="needAssessment">
                                Val. pendiente
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check m-auto">
                            <input class="form-check-input" type="checkbox" name="needPhoto" id="needPhoto">
                            <label class="form-check-label terms-label" for="needPhoto">
                                Foto. pendiente
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check m-auto">
                            <input class="form-check-input" type="checkbox" name="isNotInWhatsappGroup" id="isNotInWhatsappGroup">
                            <label class="form-check-label terms-label" for="isNotInWhatsappGroup">
                                No pertenece
                            </label>
                        </div>
                    </td>
                </tr>
            @foreach ($users as $user)
                <tr class="user-row">
                    <td>{{ $user->id }}</td>
                    <td><a class="client-icon theme-color" href="{{route('visitarPerfil', ['user'=> $user->slug]) }}"><div style="max-height:3rem; overflow:hidden">{{$user->nombre . ' ' .  $user->apellido_1 . ' ' .  $user->apellido_2}}</div></a></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->telefono }}</td>
                    <td>
                        <select onchange="onChangeAssignment({{ $user->id }},this.value)" {{!Auth::user()->hasFeature(\App\Utils\FeaturesEnum::CHANGE_CLIENT_FOLLOWER) ? 'disabled' : ''}}>
                            <option style="color: black" value="" disabled selected>Seleccione...</option>
                            @foreach ($clientFollowers as $clientFollower)
                                <option value="{{ $clientFollower->id }}" {{$user->assigned_id == $clientFollower->id ? 'selected' : ''}}>{{ $clientFollower->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>{{ $user->expiration_date ? str_limit($user->expiration_date, 10) : '' }}</td>
                    <td>
                        <a class="client-icon theme-color" href="{{route('healthTest', ['user' => $user->slug])}}">
                            @if($user->physical_assessments_created_at)
                                {{ $user->physical_assessments_created_at }}
                            @else
                                Valoración no realizada o incompleta
                            @endif
                        </a>
                    </td>
                    <td>
                        <input type="checkbox" onclick="onChangePhotoStatus({{ $user->id }},this)" data-user-id="{{ $user->id }}" {{ $user->physical_photo ? 'checked' : '' }}>
                    </td>
                    <td>
                        <input type="checkbox" onclick="onChangeWhatsappStatus({{ $user->id }},this)" data-user-id="{{ $user->id }}" {{ $user->wa_group ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
@endsection
@push('scripts')
    <script>
        // Función para realizar la llamada AJAX genérica
        function performAjaxRequest(url, data) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            }).then(response => {
                if (response.ok) {
                    console.log('Operación exitosa');
                } else {
                    console.error('Error en la operación');
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        }

        // Función para actualizar el estado de asignación
        function onChangeAssignment(userId, godmotherId) {
            performAjaxRequest("{{ route('assigned.update') }}", {
                userId: userId,
                assigned: godmotherId
            });
        }

        function onChangePhotoStatus(userId, checkbox){
            performAjaxRequest(`/update-physical-photo-status/${userId}`, {
                physical_photo: $(checkbox).is(':checked')
            });
        }

        function onChangeWhatsappStatus(userId, checkbox){
            performAjaxRequest(`/updateWaGroupStatus/${userId}`, {
                wa_group: $(checkbox).is(':checked')
            });
        }

        $(document).ready(function() {
            @if($clientFollowers)
                let options = @foreach ($clientFollowers as $clientFollower)
                    '<option value="{{$clientFollower->id}}" >{{ $clientFollower->nombre }}</option>' @if(!$loop->last)+@endif
                @endforeach
            @endif

            function filter() {
                var idValue = $('#id').val();
                var nameValue = $('#name').val();
                var emailValue = $('#email').val();
                var phoneValue = $('#phone').val();
                var needAssessmentValue = $('#needAssessment').prop('checked');
                var needPhotoValue = $('#needPhoto').prop('checked');
                var isNotInWhatsappGroupValue = $('#isNotInWhatsappGroup').prop('checked');
                var assignedValue = $('#assigned').val();
                var expirationTypeValue = $('input[name="expirationType"]:checked').val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/users/search',
                    method: 'GET',
                    data: {
                        id: idValue,
                        name: nameValue,
                        email: emailValue,
                        phone: phoneValue,
                        needAssessment: needAssessmentValue,
                        needPhoto: needPhotoValue,
                        isNotInWhatsappGroup: isNotInWhatsappGroupValue,
                        assigned: assignedValue,
                        expirationType: expirationTypeValue,
                    },
                    dataType: 'json',
                    success: function (data) {
                        // Limpiar la tabla
                        $('tbody[name="table"] .user-row').remove();
                        var expirationTypeValue = $('input[name="expirationType"]:checked').val();
                        if(expirationTypeValue == "active"){
                            $('#expirationType2Label').text('Activos: ' + data.length);
                        }
                        data.forEach(function (result) {
                            let assessmentText = result.physical_assessments_created_at
                                ? result.physical_assessments_created_at
                                : 'Valoración no realizada o incompleta';

                            $('tbody[name="table"]').append(
                                '<tr class="user-row" id=row_' + result.id + '>' +
                                '<td>' + result.id + '</td>' +
                                '<td><a class="client-icon theme-color" href="{{env('APP_URL')}}/visitar/' + result.slug + '"><div style="max-height:3rem; overflow:hidden">' + result.nombre + ' ' + result.apellido_1 + ' ' + result.apellido_2 + '</div></a></td>' +
                                '<td>' + result.email + '</td>' +
                                '<td>' + result.telefono + '</td>' +
                                '<td>' +
                                '<select id="select_' + result.id + '" onchange="onChangeAssignment(' + result.id + ', this.value)"' + '{{!Auth::user()->hasFeature(\App\Utils\FeaturesEnum::CHANGE_CLIENT_FOLLOWER) ? "disabled" : ''}}' + '>' +
                                '<option style="color: black" value="" disabled selected>Seleccione...</option>' +
                                options +
                                '</select>' +
                                '</td>' +
                                '<td>' + (result.expiration_date ? result.expiration_date.slice(0, 10) : '') + '</td>' +
                                '<td><a class="client-icon theme-color" href="/user/' + result.slug + '/wellBeingTest">' + assessmentText + '</a></td>' +
                                '<td><input type="checkbox" onclick="onChangePhotoStatus(' + result.id + ', this)" data-user-id="' + result.id + '" ' + (result.physical_photo == 1 ? 'checked' : '') + '></td>' +
                                '<td><input type="checkbox" onclick="onChangeWhatsappStatus(' + result.id + ', this)" data-user-id="' + result.id + '" ' + (result.wa_group == 1 ? 'checked' : '') + '></td>' +
                                '</tr>'
                            );

                            if (result.assigned_id) {
                                $('#select_' + result.id).val(result.assigned_id);
                            }
                            if (result.expiration_date) {
                                var today = new Date();
                                today.setHours(0, 0, 0, 0);
                                const expirationDate = new Date(result.expiration_date);
                                if (expirationDate < today) {
                                    $('#row_' + result.id).addClass('bg-danger');
                                } else {
                                    $('#row_' + result.id).addClass('bg-success');
                                }
                            }
                        });
                        $('.pagination').hide();
                    },
                    error: function(data) {
                        alert('Error filtering users');
                    }
                });
            }

            $('#id, #name, #email, #phone, #assigned').on('input', function() {
                filter();
            });

            $('#needAssessment, #needPhoto, #isNotInWhatsappGroup').on('change', function() {
                filter();
            });

            $('input[name="expirationType"]').change(function(){
                filter();
            });
        });
    </script>
@endpush
