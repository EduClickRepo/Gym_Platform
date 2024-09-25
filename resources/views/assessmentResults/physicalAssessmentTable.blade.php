<table class="table">
    <thead>
    <tr>
        <th scope="col"></th>
        @foreach($datasets as $dataset)
            <th scope="col">{{$dataset["label"]}}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @php
        $dates = json_decode($physicalAssessmentDates, true);
    @endphp
    @foreach($dates as $index => $date) <!-- Aquí usamos $index para alinear los datos -->
    <tr>
        <th scope="row">{{ $date }}</th>
        @foreach($datasets as $dataset)
            <td>{{ $dataset["data"][$index] ?? '' }}</td> <!-- Aquí usamos $index para acceder al dato correcto -->
        @endforeach
    </tr>
    @endforeach
    </tbody>
</table>
