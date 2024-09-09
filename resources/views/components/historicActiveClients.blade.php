@push('head-content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
<form action="{{ route('statistics') }}" method="GET" class="text-center mb-5">
    @csrf
    <label for="start_date">Fecha de inicio:</label>
    <input type="date" name="start_date" id="start_date">

    <label for="end_date">Fecha de fin:</label>
    <input type="date" name="end_date" id="end_date">

    <button type="submit" class="btn btn-primary">Enviar</button>
</form>

<h2 class="section-title text-center">Historico clientes activos:</h2>
<div class="themed-block col-12 col-md-10 mx-auto mt-4 p-2">
    <x-chart id="historic-active-users" type="line" :labels="$dates" :datasets="$activeClientsDatasets" ></x-chart>
<h2 class="section-title text-center">Historico clientes retenidos:</h2>
<div class="themed-block col-12 col-md-10 mx-auto mt-4 p-2">
    <x-chart id="historic-retained-users" type="line" :labels="$dates" :datasets="$retainedClientsDataset" ></x-chart>
</div>
<h2 class="section-title text-center">Historico Porcentaje de clientes retenidos:</h2>
<div class="themed-block col-12 col-md-10 mx-auto mt-4 p-2">
    <x-chart id="historic-percent-retained-users" type="line" :labels="$dates" :datasets="$percentRetainedClientsDataset" ></x-chart>
</div>
</div>
