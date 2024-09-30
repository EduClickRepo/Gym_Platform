<?php

namespace App\Http\Controllers;



use App\User;
use App\Utils\RolsEnum;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends controller
{

    public function index()
    {
        $users = DB::table('usuarios')
            ->join('client_plans', 'usuarios.id', '=', 'client_plans.client_id')
            ->leftJoin('physical_assessments', 'usuarios.id', '=', 'physical_assessments.user_id')
            ->orderBy('client_plans.expiration_date', 'desc')
            ->orderBy('physical_assessments.created_at', 'desc')
            ->orderBy('usuarios.id', 'desc')
            ->select('usuarios.*', 'client_plans.expiration_date', 'physical_assessments.created_at as physical_assessments_created_at')
            ->paginate(15);
        $clientFollowers = User::join('user_roles', 'usuarios.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', RolsEnum::CLIENT_FOLLOWER->value)->select('usuarios.*')->get();
        return view('users', [
            'users' => $users,
            'clientFollowers' => $clientFollowers
        ]);
    }

    public function updateWaGroupStatus(Request $request, User $user)
    {
        $user->wa_group = $request->wa_group;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function updatePhysicalPhotoStatus(Request $request, User $user)
    {
        $user->physical_photo = $request->physical_photo;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $isNotInWhatsappGroup = $request->input('isNotInWhatsappGroup');
        $needPhoto = $request->input('needPhoto');
        $needAssessment = $request->input('needAssessment');
        $assigned = $request->input('assigned');
        $expirationType = $request->input('expirationType');

        $query = User::query();

        if ($id) {
            $query->where('usuarios.id', $id);
        }
        if ($name) {
            $query->where(function ($query) use ($name) {
                $query->where('usuarios.nombre', 'LIKE', "%$name%")
                    ->orWhere('usuarios.apellido_1', 'LIKE', "%$name%")
                    ->orWhere('usuarios.apellido_2', 'LIKE', "%$name%");
            });
        }
        if ($email) {
            $query->where('usuarios.email', 'LIKE', "%$email%");
        }
        if ($assigned) {
            $query->where('usuarios.assigned_id', 'LIKE', "%$assigned%");
        }
        if ($phone) {
            $query->where('usuarios.telefono', 'LIKE', "%$phone%");
        }
        if ($needPhoto === "true") {
            $query->where('usuarios.physical_photo', '=', 0);
        }
        if ($isNotInWhatsappGroup === "true") {
            $query->where('usuarios.wa_group', '=', 0);
        }
        $query->leftJoin('physical_assessments', 'usuarios.id', '=', 'physical_assessments.user_id');
        if ($needAssessment === "true") {
            $query->where(function ($query) {
                $query->whereNull('physical_assessments.user_id')
                    ->orWhere(function ($query)  {
                        $query->where('physical_assessments.created_at', '<', Carbon::today()->subMonths(MONTHS_FOR_NEW_HEALTH_ASSESSMENT)->format('Y-m-d'))
                            ->whereRaw('physical_assessments.created_at = (
                                SELECT MAX(pa.created_at)
                                FROM physical_assessments pa 
                                WHERE pa.user_id = usuarios.id
                            )');
                    });
            });
        }
        $currentDate = Carbon::today();
        switch ($expirationType){
            case "all":
                $query->leftJoin('client_plans', 'usuarios.id', '=', 'client_plans.client_id');
                break;
            case "active":
                $query->join('client_plans', function ($join) use ($currentDate) {
                    $join->on('usuarios.id', '=', 'client_plans.client_id')
                        ->where('client_plans.expiration_date', '>=', $currentDate->copy()->startOfDay())
                        ->where(function ($query)  {
                            $query->where('remaining_shared_classes', '>', 0)
                                ->orWhereNull('remaining_shared_classes');
                        });
                });
                break;
            case "inactive":
                $query->join('client_plans', function ($join) use ($currentDate) {
                    $join->on('usuarios.id', '=', 'client_plans.client_id')
                        ->where(function ($query) use ($currentDate) {
                            $query->where('client_plans.expiration_date', '<', $currentDate->copy()->startOfDay())
                                ->orWhere('remaining_shared_classes', '=', 0);
                        });
                })->whereNotIn('usuarios.id', function ($query) use ($currentDate) {
                    $query->select('cpa.client_id')
                        ->from('client_plans as cpa')
                        ->where('cpa.expiration_date', '>=', $currentDate->copy()->startOfDay())
                        ->where(function ($query)  {
                            $query->where('remaining_shared_classes', '>', 0)
                                ->orWhereNull('remaining_shared_classes');
                        });
                });
                break;
        }
        $users = $query->selectRaw('usuarios.*, MAX(expiration_date) as expiration_date, MAX(physical_assessments.created_at) as physical_assessments_created_at')
            ->orderBy('expiration_date', 'DESC')
            ->groupBy('usuarios.id')
            ->get();
        return response()->json($users);
    }

    public function updateAssigned(Request $request): JsonResponse
    {
        if(!Auth::user()->hasFeature(\App\Utils\FeaturesEnum::CHANGE_CLIENT_FOLLOWER)){
            return response()->json(['success' => true]);
        }
        $userId = $request->input('userId');
        $assigned = $request->input('assigned');
        User::where('id', $userId)->update(['assigned_id' => $assigned]);

        return response()->json(['success' => true]);
    }
}
