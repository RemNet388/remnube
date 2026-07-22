<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Afip;

class FacturacionController extends Controller
{
    public function index()
    {
        return view('facturacion');
    }

    public function emitir(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string',
            'importe'     => 'required|numeric',
        ]);

        try {
            /*$afip = new Afip([
                'CUIT' => 20409378472,          // CUIT de desarrollo
                'access_token' => 'ZuqI6cjsGDej0aadkK06uhDY3r9zZCmniC2CEhTU4KdrBOTwDQKySu8aCPEs5sMF',
            ]);*/

$afip = new Afip([
    'CUIT' => 20307821061,
    'production' => false,
    'access_token' => 'ZuqI6cjsGDej0aadkK06uhDY3r9zZCmniC2CEhTU4KdrBOTwDQKySu8aCPEs5sMF',
'cert' => file_get_contents(storage_path('app/public/afip/certificado.crt')),
'key'  => file_get_contents(storage_path('app/public/afip/clave.key')),
]);


            $ptoVta = 1;
            $tipo   = 6; // Factura B
            $total  = floatval($request->importe);

            $last   = $afip->ElectronicBilling->GetLastVoucher($ptoVta, $tipo);
            $next   = $last + 1;

$data = [
    'CantReg'   => 1,
    'PtoVta'    => $ptoVta,
    'CbteTipo'  => 6,
    'Concepto'  => 1,

    'DocTipo'   => 99,
    'DocNro'    => 0,

    'CbteDesde' => $next,
    'CbteHasta' => $next,
    'CbteFch'   => intval(date('Ymd')),

    'ImpTotal'  => $total,
    'ImpNeto'   => $total,
    'ImpOpEx'   => 0,
    'ImpTotConc'=> 0,
    'ImpIVA'    => 0,

    'MonId'     => 'PES',
    'MonCotiz'  => 1,

    'CondicionIVAReceptorId' => 5, // Consumidor final

    'Iva' => [
        [
            'Id' => 3,        // Exento / no gravado
            'BaseImp' => $total,
            'Importe' => 0,
        ]
    ],
];



            // Emitir factura
            $res = $afip->ElectronicBilling->CreateVoucher($data);

            return response()->json([
                'success'  => true,
                'cbte_nro' => $next,
                'cae'      => $res['CAE'] ?? null,
                'vto_cae'  => $res['CAEFchVto'] ?? null,
                'raw'      => $res
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => true,
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}
