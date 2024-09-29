<?php

namespace App\Console\Commands;

use App\ClassType;
use App\Http\Controllers\EventController;
use App\Http\Services\KangooService;
use App\Model\Cliente;
use App\Model\SesionCliente;
use App\Utils\PlanTypesEnum;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidarKangoosReservados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validator:kangosReservados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando deja disponibles los kangoos que no confirmaron el pago de las reservas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SesionCliente::where('reservado_hasta', '<', Carbon::now()->subMinutes(5))->delete();
        $eventsController = app(EventController::class);
        $nextSession = $eventsController->loadNextSessions(1, ClassType::where('type', PlanTypesEnum::KANGOO->value)->first()->id);
        if($nextSession){
            $kangooService = app(KangooService::class);
            $event = $nextSession[0];
            $currentDate = Carbon::now();
            $startDateTime = Carbon::parse($event->fecha_inicio->format('Y-m-d') . ' ' . $event->start_hour);
            $endDateTime = Carbon::parse($event->fecha_fin->format('Y-m-d') . ' ' . $event->end_hour);
            $diffInMinutes = $currentDate->diffInMinutes($startDateTime, false);
            if($diffInMinutes <= env('MINUTES_TO_REORGANISE_KANGOOS', 5) && $diffInMinutes >= 0){

                $clientsSession = $event->attendees()->join('clientes', 'cliente_id', '=', 'clientes.usuario_id')
                    ->orderBy('talla_zapato', 'ASC')->get();
                $reasignedKangoos = [];
                $ignoredClientsSession = $clientsSession->pluck('id');
                foreach ($clientsSession as $clientSession) {
                    $user = Cliente::find($clientSession->usuario_id);
                    $kangooId = $kangooService->assignKangoo($user->talla_zapato, $user->peso()->peso, $startDateTime, $endDateTime, true, $reasignedKangoos, $ignoredClientsSession);
                    $clientSession->kangoo_id = $kangooId;
                    $cases[] = "WHEN {$clientSession->id} THEN {$kangooId}";
                    $ids[] = $clientSession->id;
                    array_push($reasignedKangoos, $kangooId);
                    //preferir tallas pequeñas, creo que ya se hace por defecto
                }
                DB::beginTransaction();
                try {
                    $idsString = implode(',', $ids);
                    $casesString = implode(' ', $cases);
                    DB::update("UPDATE sesiones_cliente SET kangoo_id = CASE id {$casesString} END WHERE id IN ({$idsString})");
                    DB::commit();
                }catch (Exception $exception) {
                    DB::rollBack();
                }
            }
        }
        Log::info('Corriendo el schedule de los kangoos');
    }
}
