<?php

namespace App\Http\Services;

use App\Exceptions\PenalizedException;
use App\Model\SesionCliente;
use App\Penalized;
use App\Repositories\FeatureRepository;
use App\Utils\FeaturesEnum;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenalizeService
{
    private FeatureRepository $featureRepository;

    public function __construct(FeatureRepository $featureRepository)
    {
        $this->featureRepository = $featureRepository;
    }
    /**
     * @throws PenalizedException
     */
    public function checkPenalizeNonAttendance($event, $client, $eventMoment)
    {
        $penalized = Penalized::where('user_id', $client->usuario_id)
            ->where('class_type',  $event->classType->id)
            ->where('from_date', '<=', $eventMoment)
            ->where('to_date', '>=', $eventMoment)
            ->first();
        if($this->featureRepository->isFeatureActive(FeaturesEnum::PENALIZE_NON_ATTENDANCE) && $penalized)
        {
            throw new PenalizedException($event->classType->type, $penalized->to_date);
        }
    }
    /**
     * @param $date
     * Los $countActiveOldClients: son clientes que renovaron su plan o que tienen planes activos hace más de un mes. Pueden ser clientes que estuvieron activos hace 1 año y volvieron
     * Los $countRetainedClients: son clientes que se les vencía su plan en el mes pasado y que renovaron
     */
    public function penalizeEvent($date):void
    {
        Log::info('Penalizing non attended');
        
        $currentDate = Carbon::parse($date);
        $startOfDay = $currentDate->copy()->startOfDay();
        $endOfDay = $currentDate->copy()->endOfDay();
        if($endOfDay > today()->endOfDay()){
            Log::info('No es posible hacer una penalización de una fecha futura');
            return;
        }

        $penalizedEventTypeIds = explode(',', env('PENALIZED_EVENT_TYPE_IDS', ''));
        $nonAttendedSessions = SesionCliente::join('eventos', 'sesiones_cliente.evento_id', 'eventos.id')
            ->whereIn('eventos.class_type_id', $penalizedEventTypeIds)
            ->where('sesiones_cliente.fecha_inicio', '>=', $startOfDay)
            ->where('sesiones_cliente.fecha_fin', '<=', $endOfDay)
            ->where('attended', 0)
            ->get();

        $endOfPenalization = $currentDate->copy()->endOfWeek()->addDays(7);

        foreach ($nonAttendedSessions as $nonAttendedSession){

            $penalized = new Penalized();
            $penalized->user_id = $nonAttendedSession->cliente_id;
            $penalized->class_type = $nonAttendedSession->class_type_id;
            $penalized->from_date = today();

            $penalized->to_date = $endOfPenalization;
            $penalized->save();

            /* If you want to delete the next session for penalization remove the assign of to_date and the save, and uncomment this code
                $sessionToDelete = SesionCliente::join('eventos', 'sesiones_cliente.evento_id', 'eventos.id')
                ->where('eventos.class_type_id', $nonAttendedSession->class_type_id)
                ->where('sesiones_cliente.fecha_inicio', '>=', today()->endOfDay())
                ->first();

            DB::transaction(function () {
            try{
                if($sessionToDelete){
                    $sessionToDelete->deleted_at = today();
                    $sessionToDelete->save();
                    $penalized->to_date = $sessionToDelete->fecha_fin;
                }else{
                    $penalized->to_date = $currentDate->addDays(7);
                }
                $penalized->save();
                } catch (Exception $exception) {
                    Log::error("ERROR SesionClienteController - cancelTraining: " . $exception->getMessage());
                    return back();
                }
            });
            */
        }
    }
}