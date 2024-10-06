@if(!$highlightSections->isEmpty())
<div id="highlightCarousel" class="carousel slide mx-auto mb-4" style="height: 75vh; width: 42.19vh" data-ride="carousel">
    <div class="carousel-inner w-100 h-100">
        @foreach($highlightSections as $highlightSection)
            <div class="carousel-item w-100 h-100 @if($loop->first)active @endif">
            @if($highlightSection->event)<a href="{{route('eventos.show',['event' => $highlightSection->event, 'date' => Carbon\Carbon::parse($highlightSection->event->fecha_inicio)->format('d-m-Y'), 'hour' => $highlightSection->event->start_hour, 'isEdited' => 0])}}">@endif
                @if($highlightSection->type=='image')
                    <img src="{{asset('images/highlightSections/'.$highlightSection->asset)}}" class="d-block w-100 h-100" alt="">
                @elseif($highlightSection->type=='video')
                    <video autoplay muted class="portrait-video landingVideo d-block w-100 h-100" src="{{asset('video/highlightSections/'.$highlightSection->asset)}}" preload="auto"></video>
                @endif
            @if($highlightSection->event)</a>@endif
            </div>
        @endforeach
    </div>
    @if($highlightSections->count() > 1)
        <button class="carousel-control-prev" type="button" data-target="#highlightCarousel" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-target="#highlightCarousel" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </button>
    @endif
</div>
@endif