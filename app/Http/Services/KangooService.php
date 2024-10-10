<?php

namespace App\Http\Services;

use App\Exceptions\NoAvailableEquipmentException;
use App\Exceptions\ShoeSizeNotSupportedException;
use App\Exceptions\WeightNotSupportedException;
use App\Model\Cliente;
use App\Model\Evento;
use App\Utils\KangooStatesEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class KangooService
{
    /**
     * @throws ShoeSizeNotSupportedException
     * @throws WeightNotSupportedException
     * @throws NoAvailableEquipmentException
     */
    public function assignKangoo(int $shoeSize, int $weight, $startDateTime, $endDateTime, $preferSmallSizes = false, $reassignedKangoos = null, $ignoredClientsSession = null)
    {

        $kangooSizes = $this->getKangooSizes($shoeSize);
        $resistance = $this->getKangooResistance($weight);

        $assignedKangooQuery =DB::table('kangoos')
            ->whereNotIn('id', function($q) use($ignoredClientsSession, $startDateTime, $endDateTime){
            $q->select('kangoos.id')->from('kangoos')
                ->leftJoin('sesiones_cliente', 'kangoos.id', '=', 'sesiones_cliente.kangoo_id')
                ->when($ignoredClientsSession, function ($query) use ($endDateTime, $startDateTime, $ignoredClientsSession) {
                    $query->whereNotIn('sesiones_cliente.id', $ignoredClientsSession);
                })
                ->where('sesiones_cliente.fecha_fin', '>', Carbon::parse($startDateTime)->format('Y-m-d H:i:s'))
                ->where('sesiones_cliente.fecha_inicio', '<', Carbon::parse($endDateTime)->format('Y-m-d H:i:s'))
                ->whereNull('sesiones_cliente.deleted_at');
            })
            ->when($reassignedKangoos, function ($query) use ($reassignedKangoos) {
                $query->whereNotIn('kangoos.id', $reassignedKangoos);
            })
            ->where('kangoos.estado', KangooStatesEnum::Available)
            ->where('kangoos.resistencia', '>=', $resistance)
            ->whereNull('kangoos.deleted_at')
            ->orderBy('kangoos.resistencia')
            ->select('kangoos.id');

        $length = count($kangooSizes);
        $start = $preferSmallSizes ? 0 : $length - 1;  // It determines if it stars from beginning or end
        $increment = $preferSmallSizes ? 1 : -1;       // It determines if it advances forward or backward
        for ($i = $start; $i >= 0 && $i < $length; $i += $increment) {
            $queryClone = clone $assignedKangooQuery;
            $queryClone->where('talla', $kangooSizes[$i]);
            $assignedKangooId = $queryClone->first();
            if ($assignedKangooId) {
                break;
            }
        }
        if (!$assignedKangooId){
            throw new NoAvailableEquipmentException();
        }
        return $assignedKangooId->id;
    }

    /**
     * @throws ShoeSizeNotSupportedException
     */
    public function getKangooSizes(int $shoeSize)
    {
        if($shoeSize < 34){
            return ["J"];
        }

        return match ($shoeSize) {
            34, 35, 36 => ["XS", "S"],
            37, 38, 39 => ["S", "M"],
            40, 41 => ["M", "L"],
            42, 43, 44 => ["L"],
            default => throw new ShoeSizeNotSupportedException(),
        };
    }

    /**
     * @throws WeightNotSupportedException
     */
    public function getKangooResistance($weight)
    {
        if ($weight < 55){
            $resistance = 1;
        }elseif ($weight < 65) {
            $resistance = 2;
        }elseif ($weight < 76) {
            $resistance = 3;
        } elseif ($weight < 80) {
            $resistance = 4;
        } elseif ($weight >= 80) {
            $resistance = 5;
        }else{
           throw new WeightNotSupportedException();
        }
        return $resistance;
    }

    public function reorderKangoos($event){
        Log::info('Optimizando organización de los kangoos...');
        try {
            $clientsSession = $event->attendees()->join('clientes', 'cliente_id', '=', 'clientes.usuario_id')
                ->whereNotNull('kangoo_id')
                ->orderBy('talla_zapato', 'ASC')->get();
            if($clientsSession->isEmpty()){
                return;
            }
            $reassignedKangoos = [];
            $ignoredClientsSession = $clientsSession->pluck('id');
            foreach ($clientsSession as $clientSession) {
                $user = Cliente::find($clientSession->usuario_id);
                $startDateTime = Carbon::parse($event->fecha_inicio->format('Y-m-d') . ' ' . $event->start_hour);
                $endDateTime = Carbon::parse($event->fecha_fin->format('Y-m-d') . ' ' . $event->end_hour);
                $kangooId = $this->assignKangoo($user->talla_zapato, $user->peso()->peso, $startDateTime, $endDateTime, true, $reassignedKangoos, $ignoredClientsSession);
                $cases[] = "WHEN $clientSession->id THEN $kangooId";
                $ids[] = $clientSession->id;
                array_push($reassignedKangoos, $kangooId);
                //preferir tallas pequeñas, creo que ya se hace por defecto
            }
            DB::beginTransaction();

            $idsString = implode(',', $ids);
            $casesString = implode(' ', $cases);
            DB::update("UPDATE sesiones_cliente SET kangoo_id = CASE id $casesString END WHERE id IN ($idsString)");
            DB::commit();
        }catch (Exception $exception) {
            Log::error("ERROR KangooService - reorderKangoos: " . $exception->getMessage());
            DB::rollBack();
        }
    }

    public function reorderKangoosOverload($id, $startDate, $startHour, $endDate, $endHour){
        $event = Evento::find($id);
        $event->fecha_inicio = $startDate;
        $event->fecha_fin = $endDate;
        $event->start_hour = $startHour;
        $event->end_hour = $endHour;
        $this->reorderKangoos($event);
    }

}