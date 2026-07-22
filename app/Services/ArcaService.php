<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ArcaService
{
    protected string $token;
    protected string $baseUrl;

    public function __construct()
    {
        // 🔥 TOKEN que me pasaste
        $this->token = "ZuqI6cjsGDej0aadkK06uhDY3r9zZCmniC2CEhTU4KdrBOTwDQKySu8aCPEs5sMF";

        // 🔥 URL correcta de ARCA WEB API
        $this->baseUrl = "https://api.arcasdk.com/v1/electronic-billing";
    }

    public function emitirFactura($ptoVta, $importe, $descripcion)
    {
        try {

            // 1️⃣ Obtener último
            $last = Http::withToken($this->token)
                ->get($this->baseUrl . "/voucher/last", [
                    "pto_vta"   => $ptoVta,
                    "cbte_tipo" => 6
                ]);

            if ($last->failed()) {
                throw new \Exception($last->body());
            }

            $lastData = $last->json();
            $next = ($lastData['cbte_nro'] ?? 0) + 1;

            // 2️⃣ Crear factura
            $data = [
                "cant_reg"  => 1,
                "pto_vta"   => $ptoVta,
                "cbte_tipo" => 6,
                "cbte_desde"=> $next,
                "cbte_hasta"=> $next,
                "concepto"  => 1,
                "doc_tipo"  => 99,
                "doc_nro"   => 0,
                "imp_total" => $importe,
                "imp_neto"  => $importe,
                "imp_iva"   => 0,
                "iva"       => [],
                "detalle"   => $descripcion,
                "mon_id"    => "PES",
                "mon_cotiz" => 1
            ];

            $res = Http::withToken($this->token)
                ->post($this->baseUrl . "/voucher/create", $data);

            if ($res->failed()) {
                throw new \Exception($res->body());
            }

            return $res->json();

        } catch (\Throwable $e) {

            json_decode($e->getMessage());
            if (json_last_error() === JSON_ERROR_NONE) {
                throw new \Exception(json_encode(json_decode($e->getMessage()), JSON_PRETTY_PRINT));
            }

            throw new \Exception(json_encode([
                "error"   => "ARCA Error",
                "detalle" => $e->getMessage()
            ], JSON_PRETTY_PRINT));
        }
    }
}
