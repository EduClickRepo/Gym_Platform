<?php

namespace App\Http\Controllers;

use App\EditedEvent;
use App\EventHour;
use App\Exceptions\NoAvailableEquipmentException;
use App\Exceptions\ShoeSizeNotSupportedException;
use App\Exceptions\WeightNotSupportedException;
use App\Feature;
use App\Http\Services\KangooService;
use App\Model\Evento;
use App\Repositories\ClientPlanRepository;
use App\Utils\DaysEnum;
use App\Utils\FeaturesEnum;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
    protected KangooService $kangooService;

    public function __construct(KangooService $kangooService)
    {
        $this->kangooService = $kangooService;
    }

    public function create(){
        return view('sessions.createSession');
    }

    /**
     * Display the specified resource.
     *
     * @param  $event
     * @return View
     */
    public function show($event, $date, $hour, $isEdited)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        if($isEdited){
            $event = EditedEvent::where('evento_id', '=', $event)
                        ->where('fecha_inicio', '=', $date)
                        ->where('start_hour', '=', $hour)
                        ->first();
            $event->id = $event->evento_id;
        } else{
            $event = Evento::find($event);
            if ($event->repeatable){
                $event->fecha_inicio = $date;
                $event->fecha_fin = $date;
                $eventHour = EventHour::where('day', '=', Carbon::parse($date)->format('l'))->where('start_hour', $hour)->first();
                $event->start_hour = $eventHour->start_hour;
                $event->end_hour = $eventHour->end_hour;
            }
        }

        $clientPlanRepository = new ClientPlanRepository();
        $clientPlan = $clientPlanRepository->findValidClientPlan($event);

        if ($clientPlan) {
            return view('sessions.event', [
                'event' => $event,
                'plan' => $clientPlan,
                'equipmentIncluded' => $clientPlan->equipment_included,
                'user' => Auth::user()
            ]);
        }
        return view('sessions.event', [
            'event' => $event,
            'user' => Auth::user()
        ]);
    }

    public function save(Request $request){
        $event = new Evento();
        $event->nombre = $request->name;
        $event->descripcion = $request->description;
        //TODO change image in front-end and assign it here
        $event->imagen = "request->img";
        $event->info_adicional = $request->info;
        $event->lugar = $request->place;
        $event->cupos = $request->quotas;
        $event->precio = $request->price;
        $event->precio_sin_implementos = $request->priceWithoutBoots;
        $event->descuento = $request->discount;
        $event->oferta = $request->offer;
        $event->repeatable = $request->repeatable;
        if($request->repeatable){
            $event->save();
            foreach ($request->diasSemana as $day){
                $day = DaysEnum::from($day);
                $eventHour = new EventHour();
                $eventHour->event_id = $event->id;
                $eventHour->day = $day->name;
                $eventHour->start_hour =date("H:i", strtotime($request->startHour[$day->value]));
                $eventHour->end_hour = date("H:i", strtotime($request->endHour[$day->value]));
                $eventHour->save();
            }
        }else{
            $event->fecha_inicio = date('Y-m-d', strtotime(str_replace('/', '-', $request->startDate)));
            $event->fecha_fin = date('Y-m-d', strtotime(str_replace('/', '-', $request->endDate)));
            $event->start_hour =date("H:i", strtotime($request->startHour));
            $event->end_hour = date("H:i", strtotime($request->endHour));
            $event->save();
        }
    }

    public function nextSessions(int $branchId = null, int $classTypeId = null){

        $events = $this->loadNextSessions($branchId, $classTypeId);

        return view('proximasSesiones', compact('events'));
    }

    public function ajaxNextSessions(Request $request){

        $events = $this->loadNextSessions(null, $request->query('classTypeId'));
        if($request->query('rentEquipment') === "true"){
            try{
                $events = $this->filterEvents($events, $request->query('shoeSize'), $request->query('weight'));
            }catch (ShoeSizeNotSupportedException | WeightNotSupportedException $e) {
                Session::put('msg_level', 'danger');
                Session::put('msg', $e->getMessage());
                Session::save();
                return response()->json(['error' => $e->getMessage()], $e->getCode());
            }
        }

        return response()->json([
            'success' => true,
            'events' => $events
        ], 200);
    }

    /**
     * @throws ShoeSizeNotSupportedException
     * @throws WeightNotSupportedException
     */
    public function filterEvents($events, int $shoeSize, int $weight){
        return $events->filter(function ($event) use ($shoeSize, $weight) {
            $startDateTime = Carbon::parse($event->fecha_inicio)->format('Y-m-d') . ' ' . $event->start_hour;
            $endDateTime = Carbon::parse($event->fecha_fin)->format('Y-m-d') . ' ' . $event->end_hour;
            try {
                $kangooId = $this->kangooService->assignKangoo($shoeSize, $weight, $startDateTime, $endDateTime);
                return $kangooId != null;
            } catch (NoAvailableEquipmentException $e){
                return false;
            }
        });
    }

    public function loadNextSessions(int $branchId = null, int $classTypeId = null){
        $scheduleUntil = Feature::where('feature', '=', 'schedule_until')->first();
        if(!$scheduleUntil || Auth::user()?->hasFeature(FeaturesEnum::SEE_EXTENDED_SCHEDULE)){
            $endDate = today()->addDays(6);
        }else{
            $endDate = $scheduleUntil->active_at;
        }

        $startDate = Carbon::now();
        $startDate->minute(0);
        $startDate->second(0);
        if(Auth::user()?->hasFeature(FeaturesEnum::SEE_ATTENDEES)){
            $startDate->subHour();
        }
        $editedEvents = EditedEvent::when($branchId, function ($query, $branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->when($classTypeId, function ($query, $classTypeId) {
                return $query->where('class_type_id', $classTypeId);
            })
            ->where('fecha_inicio', '>=', $startDate) //It is only comparing by date because if it compares also with hour the repeated events that were edited will not be filtered
            ->where('fecha_fin', '<=', $endDate)
            ->orderBy('fecha_inicio', 'asc')
            ->get()->map(function($element) {
                $element['id'] = $element->evento_id;
                return $element;
            });

        $uniqueEvents = Evento::
            whereDoesntHave('edited_events', function (Builder $query) use ($endDate, $startDate) {
                $query->whereRaw('CONCAT(fecha_inicio, " ", start_hour) >= ?', [$startDate])
                    ->whereRaw('CONCAT(fecha_fin, " ", end_hour) <= ?', [$endDate]);
            })
            ->when($branchId, function ($query, $branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->when($classTypeId, function ($query, $classTypeId) {
                return $query->where('class_type_id', $classTypeId);
            })
            ->where('repeatable', '=', false)
            ->whereRaw('CONCAT(fecha_inicio, " ", start_hour) >= ?', [$startDate])
            ->whereRaw('CONCAT(fecha_fin, " ", end_hour) <= ?', [$endDate])
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        $repeatableEvents = Evento::selectRaw(
            'eventos.*, event_hours.*, eventos.id as id'
            )
            ->when($branchId, function ($query, $branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->when($classTypeId, function ($query, $classTypeId) {
                return $query->where('class_type_id', $classTypeId);
            })
            ->where('repeatable', '=', true)
            ->join('event_hours', 'eventos.id', 'event_hours.event_id')
            ->get();

        $events = $editedEvents->where('deleted', '==', 0)->concat($uniqueEvents);

        $dateTime =  Carbon::now();
        for ($i = 0; $i <  Carbon::today()->diffInDays($endDate, false)+1; $i++) {
            $dayName = $dateTime->format('l');

            $updatedCollection = $repeatableEvents->where('day', '=', $dayName)
                ->map(function($element) use ($dateTime, $editedEvents) {
                    $elementCopy = clone $element;

                    if ($editedEvents->filter(function ($model) use ($dateTime, $elementCopy) {
                        return $model->evento_id == $elementCopy->id && $model->fecha_inicio->equalTo($dateTime->format('Y-m-d'));
                    })->count() > 0) {
                    return null;
                }else{
                    $elementCopy['id'] = $elementCopy->event_id;
                    $elementCopy['fecha_inicio'] = $dateTime->format('d-m-Y');
                    $elementCopy['fecha_fin'] = $dateTime->format('d-m-Y');
                    return $elementCopy;
                }
            })->filter(function($item) {
                return $item !== null;
            });

            $events = $events->concat($updatedCollection);
            $dateTime = $dateTime->addDay();
        }

        $events = $events->filter(function ($event) use ($startDate) {
            $eventStartTime = Carbon::parse($event->fecha_inicio->format('Y-m-d') . ' ' . $event->start_hour);
            $eventEndTime = Carbon::parse($event->fecha_fin->format('Y-m-d') . ' ' . $event->end_hour);
            return $eventStartTime->gte($startDate) || $eventEndTime->gte($startDate);
        })->sortBy([
                ['fecha_inicio', 'asc'],
                ['start_hour', 'asc'],
        ])->values()->all();
        return $events;
    }

    public function reorderKangoos(Request $request){
        $this->kangooService->reorderKangoosOverload($request->eventId, $request->startDate, $request->startHour, $request->endDate, $request->endHour);
    }
}
