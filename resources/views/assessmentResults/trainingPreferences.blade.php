@if($trainingPreferences)
    <div class="themed-block col-12 col-md-10 mx-auto mt-4 p-2">
        <h3 class="section-title">Preferencias de entrenamiento:</h3>
        @if($trainingPreferences->training_frequency)
            <p>Frecuencia: {{$trainingPreferences->training_frequency}} veces por semana</p>
        @endif
        @if($trainingPreferences->intensity)
            <p>Intensidad: {{$trainingPreferences->intensity}}</p>
        @endif
        @if($trainingPreferences->music)
            <p>Música: {{$trainingPreferences->music}}</p>
        @endif
        @if($trainingPreferences->training_type)
            <p>Deporte Favorito: {{$trainingPreferences->training_type}}</p>
        @endif
    </div>
@endif
