@isset($physicalAssessments)
    <div class="themed-block col-12 col-md-10 mx-auto mt-4 p-2">
        <h2 class="section-title">Valoración Física:</h2>
        <p>Última Valoración: {{$physicalAssessments?->last()?->created_at?->format('Y-m-d')}}</p>

        <x-chart id="physicalChart" type="line" :labels="$physicalAssessmentDates" :datasets="$datasets"></x-chart>

        <br/>
        @include('assessmentResults.physicalAssessmentTable')
    </div>
@endisset
