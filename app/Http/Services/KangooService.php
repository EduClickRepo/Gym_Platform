<?php

namespace App\Http\Services;

use App\Exceptions\NoAvailableEquipmentException;
use App\Exceptions\ShoeSizeNotSupportedException;
use App\Exceptions\WeightNotSupportedException;
use App\Utils\KangooStatesEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        switch ($shoeSize){
            case 34:
            case 35:
            case 36:
                return ["XS", "S"];
            case 37:
            case 38:
            case 39:
                return ["S", "M"];
            case 40:
            case 41:
                return ["M", "L"];
            case 42:
            case 43:
            case 44:
                return ["L"];
            default:
                throw new ShoeSizeNotSupportedException();
        }
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

}