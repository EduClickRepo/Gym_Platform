<?php

namespace App\Http\Services;

use App\Model\ClientPlan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements SendMessageInterface
{

    public function sendMessage($info)
    {
        Log::info("Enviando mensaje de renovación de plan, al telefono, " . $info->cellphone);
        $response = Http::withToken(env('WHATSAPP_TOKEN', 'EAAYtwx6pwEIBO2kuMIVzPZAAE4YkcUxLNMIuZAiASqnbwjR83XW1dgmUdZCI049vTK3ImX6VTZALPUrxdFdtroV3Hc9NcTLzZB6bSPMHCwf6XtelpjeHbFYHFwFPqPRKzBzH0bTRYNegb26w3xzpRzN183JzbfGtTqwpTbyy6vPTif9UmgtBQ7G85F4LON7Sd7xRCJRgMXxyIeInL0cMoTG4h1ohPv1EZD'))
            ->post('https://graph.facebook.com/v19.0/'.env('WHATSAPP_NUMBER_ID').'/messages',
                [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => '+57'.$info->cellphone,
                    'type' => 'template',
                    'template' => [
                        'name' => 'plan_expiration_notice',
                        'language' => [
                            'code' => 'es'
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    [
                                        'type' => 'text',
                                        'text' => $info->expiration_date->translatedFormat('l j \d\e F \d\e Y')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        if ($response->status() != 200) {
            Log::error('El envio del mensaje de renovación de plan, al telefono: ' . $info->cellphone . ' no fue exitosa. StatusCode: ' . $response->status());
            return;
        }
        ClientPlan::find($info->client_plan_id)
            ->update(['scheduled_renew_msg' => '1']);
        Log::info('Envío exitoso de mensaje de renovación de plan, al telefono: ' . $info->cellphone);
    }
}