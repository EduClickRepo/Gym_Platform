<?php

namespace App\Http\Controllers;

use App\TyC;
use App\TyCUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TyCController extends Controller
{

    public function signTyC(Request $request)
    {
        $lastTyC = TyC::orderBy('created_at', 'desc')->first();
        TyCUser::create([
            'user_id' => Auth::id(),
            'tyc_id' => $lastTyC->id
        ]);
        Session::forget('show_terms_modal');
        return redirect()->back();
    }
}
