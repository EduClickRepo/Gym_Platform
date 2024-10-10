<?php

namespace App\Console\Commands;

use App\ClassType;
use App\Http\Controllers\EventController;
use App\Http\Services\KangooService;
use App\Model\SesionCliente;
use App\Utils\PlanTypesEnum;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
        Log::info('Corriendo el schedule de los kangoos...');
        SesionCliente::where('reservado_hasta', '<', Carbon::now()->subMinutes(5))->delete();
        $eventsController = app(EventController::class);
        $nextSession = $eventsController->loadNextSessions(1, ClassType::where('type', PlanTypesEnum::KANGOO->value)->first()->id);
        if($nextSession){
            $kangooService = app(KangooService::class);
            $event = $nextSession[0];
            $currentDate = Carbon::now();
            $startDateTime = Carbon::parse($event->fecha_inicio->format('Y-m-d') . ' ' . $event->start_hour);
            $diffInMinutes = $currentDate->diffInMinutes($startDateTime, false);
            if($diffInMinutes <= env('MINUTES_TO_REORGANISE_KANGOOS', 10) && $diffInMinutes >= 0){
                $kangooService->reorderKangoos($event);
            }
        }
    }
}
