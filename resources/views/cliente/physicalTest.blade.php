<div id="physical-section" class="themed-block p-3" style="display: none;">
    <h1 class="text-center">Valoración Física</h1>
    <form id="physicalForm">
        @csrf
        <x-input name="weight" description="Peso (Kg)" type="number" required step=".01"></x-input>
        <x-input name="body_fat" description="Body Fat" type="number" required step=".01"></x-input>
        <x-input name="muscle" description="Muscle" type="number" required step=".01"></x-input>
        <x-input name="basal_metabolism" description="RM Kcal:" type="number" required step=".01"></x-input>
        <x-input name="body_age" description="Body Age" type="number" required step="1"></x-input>
        <x-input name="visceral_fat" description="Visceral Fat" type="number" required step="1"></x-input>

        <!--<x-input name="water_level" description="Nivel agua:" type="number" required step=".01"></x-input>-->
        <!--<x-input name="proteins" description="Proteínas:" type="number" required step=".01"></x-input>-->
        <!--<x-input name="bone_mass" description="Masa Ósea:" type="number" required step=".01"></x-input>-->
        <!--<x-input name="body_score" description="Puntuación Corporal:" type="number" required step=".01"></x-input>-->
        <div class="d-flex justify-content-between">
            <p class="btn btn-danger mt-3 mx-auto" onclick="hidePhysicalSection()">Quitar Sección física</p>
            <button class="btn themed-btn mt-3 mx-auto d-block" type="submit">Guardar Sección</button>
        </div>
    </form>
</div>
<p class="mt-3 mx-auto text-center cursor-pointer" id="showPhysicalSection" onclick="showPhysicalSection()">+ Sección fisica</p>


@push('scripts')
    <script>
        function showPhysicalSection() {
            const section = document.getElementById('physical-section');
            document.getElementById('showPhysicalSection').style.display = 'none';
            section.style.display = 'block';
        }
        function hidePhysicalSection() {
            const section = document.getElementById('physical-section');
            document.getElementById('showPhysicalSection').style.display = 'block';
            section.style.display = 'none';
        }

        function handleAjaxResponse(data) {
            appendAjaxAlert(data.msg, data.level);
        }

        function savePhysicalTest(){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('savePhysicalTest') }}",
                method: "POST",
                data: {
                    user_id : {{$user->id}},
                    muscle : document.getElementById('muscle')?.value,
                    visceral_fat: document.getElementById('visceral_fat')?.value,
                    body_fat: document.getElementById('body_fat')?.value,
                    water_level: document.getElementById('water_level')?.value,
                    proteins: document.getElementById('proteins')?.value,
                    basal_metabolism: document.getElementById('basal_metabolism')?.value,
                    bone_mass: document.getElementById('bone_mass')?.value,
                    body_score: document.getElementById('body_score')?.value,
                    weight : document.getElementById('weight')?.value,
                    body_age: document.getElementById('body_age')?.value,
                },
                success: handleAjaxResponse,
                error: handleAjaxResponse
            });
        }

        $(document).ready(function() {
            $('#physicalForm').submit(function (event) {
                event.preventDefault();
                savePhysicalTest();
            });
        });
    </script>
@endpush