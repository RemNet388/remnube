<?php
// test_afip.php
require __DIR__ . '/vendor/autoload.php';

use Afip;

// Rutas absolutas a los certificados
$certPath = __DIR__ . '/storage/logs/certificado.crt';
$keyPath  = __DIR__ . '/storage/logs/key.key';

// Verificar que existan
echo "Cert existe? " . (file_exists($certPath) ? "SI" : "NO") . PHP_EOL;
echo "Key existe? " . (file_exists($keyPath) ? "SI" : "NO") . PHP_EOL;

try {
    // Instancia AFIP
    $afip = new Afip([
        'CUIT'       => 20307821061,
        'cert'       => $certPath,
        'key'        => $keyPath,
        'production' => false
    ]);

    // Estado de los servidores
    $status = $afip->ElectronicBilling->GetServerStatus();
    echo "Estado servidores:" . PHP_EOL;
    var_dump($status);

    // Último comprobante emitido en punto de venta 3, tipo 6 (Factura B / Ticket)
    $lastVoucher = $afip->ElectronicBilling->GetLastVoucher(3, 6);
echo "Ultimo: ".$lastVoucher;

    // Datos del comprobante de prueba
    $data = [
        'CantReg' => 1,
        'PtoVta' => 3,
        'CbteTipo' => 6,
        'Concepto' => 1,
        'DocTipo' => 99,
        'DocNro' => 0,
        'CbteDesde' => $lastVoucher + 1,
        'CbteHasta' => $lastVoucher + 1,
        'CbteFch' => intval(date('Ymd')),
        'ImpTotal' => 100,
        'ImpTotConc' => 0,
        'ImpNeto' => 100,
        'ImpOpEx' => 0,
        'ImpIVA' => 0,
        'ImpTrib' => 0,
        'MonId' => 'PES',
        'MonCotiz' => 1,
    ];

    // Emitir comprobante
    $res = $afip->ElectronicBilling->CreateVoucher($data);

    echo "Resultado emisión:" . PHP_EOL;
    var_dump($res);

    echo "CAE: " . $res['CAE'] . PHP_EOL;
    echo "Vto CAE: " . $res['CAEFchVto'] . PHP_EOL;
    echo "Número de comprobante: " . $res['voucher_number'] . PHP_EOL;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
