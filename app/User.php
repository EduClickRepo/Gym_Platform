<?php

namespace App;

use App\Model\Blog;
use App\Model\Cliente;
use App\Model\Entrenador;
use App\Model\Review;
use App\Utils\FeaturesEnum;
use App\Utils\RolsEnum;
use Assada\Achievements\Achiever;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable, Achiever;

    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'apellido_1',
        'apellido_2',
        'email',
        'telefono',
        'password',
        'nivel',
        'slug',
        'foto',
        'document_id',
        'eps',
        'marital_status',
        'instagram',
        'emergency_contact',
        'emergency_phone',
        'assigned_id',
        'occupation'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['fecha_nacimiento'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function reviews()
    {
        return $this->belongsToMany(Review::class, 'reviews_user', 'user_id', 'review_id');
    }

    public function rating()
    {
        $rating = $this->reviews()->average('rating');
        return $rating;
    }

    public function ratingPorcentage()
    {
        $rating = $this->reviews()->average('rating') / 5;
        return $rating;
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id', 'usuario_id');
    }

    public function entrenador()
    {
        return $this->belongsTo(Entrenador::class, 'id', 'usuario_id');
    }

    public function getEdadAttribute()
    {
        if ($this->fecha_nacimiento == null) {
            return '';
        }
        return str_after(Carbon::parse($this->fecha_nacimiento)->diffForHumans(), 'hace ');
    }

    public function getFullNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido_1 . ' ' . $this->apellido_2;
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'usuario_id', 'id');
    }

    public function isAdmin()
    {
        return $this->rol === 'admin';
    }

    public function getPhysicalAssessmentAttribute(): object|null
    {
        return $this->physicalAssessments('desc')->first();
    }

    public function physicalAssessments($orden = 'asc'): HasMany
    {
        return $this->hasMany(PhysicalAssessment::class, 'user_id', 'id')->orderBy('created_at', $orden);
    }

    public function comments($order = 'desc')
    {
        return $this->hasMany(UserComment::class, 'user_id', 'id')
            ->where('reply_id', null)
            ->orderBy('created_at', $order);
    }

    public function hasRole(RolsEnum $rol): bool
    {
        $hasRol = DB::table('user_roles')
            ->where('user_roles.user_id', $this->id)
            ->where('user_roles.role_id', $rol->value)
            ->get();
        return $hasRol->isNotEmpty();
    }

    public function hasTyC(int $tyc_id): bool
    {
        $tyc = DB::table('tyc_user')
            ->where('user_id', $this->id)
            ->where('tyc_id', $tyc_id)
            ->get();
        return $tyc->isNotEmpty();
    }


    public function hasFeature(FeaturesEnum $feature): bool
    {
        $featureId = Feature::where('title', '=', $feature->value)->first()?->id;
        if(!$featureId){
            return false;
        }
        $hasFeature = DB::table('role_features')
            ->join('user_roles', 'role_features.role_id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $this->id)
            ->where('role_features.feature_id', $featureId)
            ->get();
        if ($hasFeature->isNotEmpty()) {
            return true;
        }

        $specialPermission = DB::table('user_features')
            ->where('user_id', $this->id)
            ->where('feature_id', $featureId)
            ->get();
        return $specialPermission->isNotEmpty();
    }

    public function getAssignedAttribute(): object|null
    {
        return User::find($this->assigned_id);
    }
}
