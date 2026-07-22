<?php

require __DIR__ . '/vendor/autoload.php';

use Afip;

// 👉 Pedimos la contraseña desde variable de entorno para no dejarla en el código
$arcaPassword = getenv('ARCA_PASSWORD');

if (!$arcaPassword) {
    die("Error: Debes exportar ARCA_PASSWORD antes de ejecutar este script.\n");
}

$afip = new Afip([
    'CUIT'       => 20307821061,
    'production' => true,
]);

// ===============================
// GENERAR CERTIFICADO DE PRODUCCIÓN
// ===============================

$result = $afip->Automations->createCertProd([
    'username' => '20307821061',    // tu usuario ARCA
    'password' => $arcaPassword,    // contraseña ARCA desde env
    'alias'    => 'RemNubePROD',    // nombre del certificado
]);

// Guardar archivos generados
file_put_contents(__DIR__ . '/cert-prod.key', $result['key']);
file_put_contents(__DIR__ . '/cert-prod.crt', $result['cert']);

echo "✔ Certificado de producción creado y guardado:\n";
echo " - cert-prod.crt\n";
echo " - cert-prod.key\n";
