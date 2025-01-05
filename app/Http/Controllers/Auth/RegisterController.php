<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\ExistingUserException;
use App\Http\Controllers\Controller;
use App\Model\Cliente;
use App\TyC;
use App\TyCUser;
use App\User;
use App\UserRole;
use App\Utils\RolsEnum;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create the realPath to rediect.
     *
     * @return void
     */
    public function redirectTo(){
        $user = Auth::user();
        $redirectTo = '/user/' . $user->slug . '/home';
        return $redirectTo;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'cellphone' => 'required|int|min:1000000000|max:9999999999|unique:usuarios,telefono',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:6',
            //'role' => 'required|string',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return User | RedirectResponse
     */
    public function create(array $data): User | RedirectResponse
    {
        DB::beginTransaction();
        try {
            $statement = DB::select("show table status like 'usuarios'");
            $id = $statement[0]->Auto_increment;
            $splitName = explode(' ', $data['name'], 3); // Restricts it to only 2 values, for names like Billy Bob Jones

            $nombre = $splitName[0];
            $primer_apellido = !empty($splitName[1]) ? $splitName[1] : ''; // If last name doesn't exist, make it empty
            $segundo_apellido = !empty($splitName[2]) ? $splitName[2] : ''; // If second last name doesn't exist, make it empty

            $user = User::create([
                'nombre' => $nombre,
                'apellido_1' => $primer_apellido,
                'apellido_2' => $segundo_apellido,
                'email' => $data['email'],
                'telefono' => $data['cellphone'],
                'password' => Hash::make($data['password']),
                'nivel' => 0,
                'slug' => $id,//por defecto se coloca el id como la URL (slug) inicial
            ]);
            UserRole::create([
                'user_id' => $id,
                'role_id' => RolsEnum::CLIENT
            ]);
            Cliente::create([
                'usuario_id' => $id
            ]);
            $lastTyC = TyC::orderBy('created_at', 'desc')->first();
            TyCUser::create([
                'user_id' => $id,
                'tyc_id' => $lastTyC->id
            ]);
            DB::commit();
            return $user;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("ERROR RegisterController - create: " . $exception->getMessage());
            Session::put('msg_level', 'danger');
            Session::put('msg', __('general.error_general'));
            Session::save();
            return redirect()->back();
        }
    }
}
