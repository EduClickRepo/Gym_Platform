<?php

namespace App\Http\Controllers;

use App\Agreement;
use App\Repositories\ClientPlanRepository;
use App\UsedAgreement;
use App\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AgreementsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $agreements = Agreement::all();
        return view('agreements', ['agreements' => $agreements]);
    }

    public function generateQr(Request $request)
    {
        $request->validate([
            'agreement_id' => 'required|exists:agreements,id',
        ]);

        $user = Auth::user();
        $clientPlanRepository = new ClientPlanRepository();
        $clientPlans = $clientPlanRepository->findValidClientPlans(clientId: $user->id);
        if($clientPlans->isEmpty()){
            return response()->json([
                'error' => 'Cliente no activo',
            ], 403);
        }
        $agreement = Agreement::findOrFail($request->agreement_id);
        $secretKey = env('AGREEMENTS_SECRET_KEY');
        $expiresAt = now()->addMinutes(5)->timestamp;
        $appUrl = env('APP_URL');


        $signature = hash_hmac('sha256', $user->id . $agreement->id . $expiresAt, $secretKey);
        $qrUrl = "{$appUrl}/api/validate-qr?user_id={$user->id}&agreement_id={$agreement->id}&expires_at={$expiresAt}&signature={$signature}";

        $qrCode = QrCode::size(300)->generate($qrUrl);

        return response()->json(['qr' => $qrCode->toHtml()]);
    }

    public function validateQr(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:usuarios,id',
            'agreement_id' => 'required|exists:agreements,id',
            'expires_at' => 'required|integer',
            'signature' => 'required|string',
        ]);

        $secretKey = env('AGREEMENTS_SECRET_KEY');
        $userId = $request->user_id;
        $agreementId = $request->agreement_id;
        $expiresAt = $request->expires_at;
        $signature = $request->signature;

        if ($expiresAt < now()->timestamp) {
            return view('validateQR', ['status' => 401]);
        }

        $validSignature = hash_hmac('sha256', $userId . $agreementId . $expiresAt, $secretKey);

        if (!hash_equals($validSignature, $signature)) {
            return view('validateQR', ['status' => 403]);
        }
        UsedAgreement::create([
            'user_id' => $userId,
            'agreement_id' => $agreementId,
        ]);
        $user = User::findOrFail($userId);
        return view('validateQR', ['status' => 200, 'user' => $user]);
    }
}
