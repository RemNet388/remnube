<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Afip;
use Exception;

class AfipTestController extends Controller
{
    public function index()
    {
        return view('ventas.afip_test');
    }

    public function enviar(Request $request)
    {
        try {
            $afip = new Afip([
                'CUIT' => 20307821061,
                'production' => false, // homologación
                'cert' => public_path('afip/certificado.crt'),
                'key'  => public_path('afip/clave.key'),
            ]);

            // TEST: Estado del servidor
            $result = $afip->ElectronicBilling->GetServerStatus();

            return back()->with('success', print_r($result, true));

        } catch (Exception $e) {

            // Capturar el error RAW de la SDK
            $errorRaw = null;
            if (property_exists($e, 'error')) {
                $errorRaw = print_r($e->error, true);
            }

            return back()->with('error', 
                "ERROR SIMPLE:\n" . $e->getMessage() .
                "\n\nERROR RAW AFIP:\n" . ($errorRaw ?? 'N/A')
            );
        }
    }
}
