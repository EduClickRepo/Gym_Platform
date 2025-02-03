<?php

namespace App\Model;

use App\Utils\Constantes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SesionCliente extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sesiones_cliente';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cliente_id','kangoo_id','evento_id', 'reservado_hasta', 'fecha_inicio', 'fecha_fin'
    ];

    /**
     * Transforms dates to carbon
     * @var string[]
     */
    protected $dates = ['created_at', 'updated_at', 'fecha_inicio', 'fecha_fin'];

    public function scopeEntrenamientosAgendados($query){
         return $query->leftJoin('edited_events', function ($join) {
                    $join->on('sesiones_cliente.evento_id', '=', 'edited_events.evento_id')
                        ->whereRaw('sesiones_cliente.fecha_inicio = CONCAT(edited_events.fecha_inicio, " ", edited_events.start_hour)');
                })
             ->leftJoin('eventos', 'sesiones_cliente.evento_id', 'eventos.id')
             ->leftJoin('kangoos', 'sesiones_cliente.kangoo_id', 'kangoos.id')
             ->where('sesiones_cliente.fecha_inicio', '>=', today())
             ->select('sesiones_cliente.id','sesiones_cliente.fecha_inicio','sesiones_cliente.fecha_fin',
                 DB::raw('COALESCE(edited_events.nombre, eventos.nombre) as nombre'),
                 DB::raw('COALESCE(edited_events.lugar, eventos.lugar) as lugar'),
                'kangoos.SKU');
    }

    public function client(){
        return $this->belongsTo(Cliente::class,'cliente_id', 'usuario_id');
    }

    public function kangoo(){
        return $this->hasOne(Kangoo::class,'id', 'kangoo_id')->withTrashed();
    }

    public function event(){
        return $this->belongsTo(Evento::class,'evento_id', 'id')->withTrashed();
    }
}
