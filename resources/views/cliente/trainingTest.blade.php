<div id="training-section" class="themed-block p-3" style="display: none;">
    <h1 class="text-center">Preferencias de entrenamiento</h1>
    <form id="trainingForm">
        @csrf
        <div class="form-group row">
            <label for="training_frequency" class="col-md-4 col-form-label text-md-center">¿Con qué frecuencia realizas actividad física?</label>
            <div class="col-md-6">
                <select class="form-control pl-1 color-white bg-dark" id="training_frequency" name="training_frequency">
                    <option value="" disabled selected>Selecciona...</option>
                    <option value="nunca" >Nunca</option>
                    <option value="1" >1 vez por semana</option>
                    <option value="2-3" >2 a 3 vez por semana</option>
                    <option value="+3" >+3 veces por semana</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="training_frequency" class="col-md-4 col-form-label text-md-center">¿Qué intensidad prefieres a la hora de entrenar?</label>
            <div class="col-md-6">
                <select class="form-control pl-1 color-white bg-dark" id="training_intensity" name="training_intensity">
                    <option value="" disabled selected>Selecciona...</option>
                    <option value="low" >baja</option>
                    <option value="medium" >media</option>
                    <option value="high" >alta</option>
                </select>
            </div>
        </div>
        <x-input name="music" description="¿Con qué música te gusta entrenar?" type="text" required></x-input>
        <x-input name="training_type" description="¿Algún tipo de deporte/entrenamiento preferido?" type="text"></x-input>
        <div class="d-flex justify-content-between">
            <button class="btn btn-danger mt-3 mx-auto" onclick="hideTrainingSection()">Quitar Sección de entrenamiento</button>
            <button class="btn themed-btn mt-3 mx-auto d-block" type="submit">Guardar Sección</button>
        </div>
    </form>
</div>
<p class="mt-3 mx-auto text-center cursor-pointer" id="showTrainingSection" onclick="showTrainingSection()">+ Sección Pref. Entrenamiento</p>

@push('scripts')
    <script>
        function showTrainingSection() {
            const section = document.getElementById('training-section');
            document.getElementById('showTrainingSection').style.display = 'none';
            section.style.display = 'block';
        }
        function hideTrainingSection() {
            const section = document.getElementById('training-section');
            document.getElementById('showTrainingSection').style.display = 'block';
            section.style.display = 'none';
        }

        function saveTrainingTest(){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('saveTrainingTest') }}",
                method: "POST",
                data: {
                    user_id : {{$user->id}},
                    training_frequency : document.getElementById('training_frequency').value,
                    intensity: document.getElementById('training_intensity').value,
                    music: document.getElementById('music').value,
                    training_type : document.getElementById('training_type').value,
                },

                success: handleAjaxResponse,
                error: handleAjaxResponse
            });
        }

        $(document).ready(function() {
            $('#trainingForm').submit(function (event) {
                event.preventDefault();
                saveTrainingTest();
            });
        });
    </script>
@endpush