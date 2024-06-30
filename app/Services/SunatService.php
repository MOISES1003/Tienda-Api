<?php

namespace App\Services;

use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\Storage;

class SunatService
{
    public function getSee($company)
    {
        // Asegúrate de que la ruta al archivo de certificado sea correcta
        $certPath = $company->cert_path;

        if (!Storage::exists($certPath)) {
            return response()->json(['message' => 'El archivo de certificado no existe en la ruta especificada.'], 404);
        }
        // Leer el contenido del certificado usando el sistema de almacenamiento de Laravel
        $Certificado = Storage::get($certPath);

        $see = new See();
        $see->setCertificate($Certificado); // Aquí ya no necesitas usar file_get_contents
        $see->setService($company->production ? SunatEndpoints::FE_BETA :  SunatEndpoints::FE_BETA);
        $see->setClaveSOL($company->ruc, $company->sol_user, $company->sol_pass);
    }
}
