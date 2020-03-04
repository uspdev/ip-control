<?php
if (PHP_SAPI == 'cli') {
} else {
    echo '<pre>';
}
echo '*** O endereço remoto é simulado ***' . PHP_EOL;
echo '*** Quando bloqueado, será gerado HTTP 403 caso não seja passado Ipcontrol::proteger(false)***' . PHP_EOL;
echo PHP_EOL;

require_once __DIR__ . '/../vendor/autoload.php';
use Uspdev\Ipcontrol\Ipcontrol;

$msg = 'Sem definir USPDEV_IP_CONTROL: ';
if (Ipcontrol::proteger()) {
    $msg .= 'Liberado por padrão';
} else {
    $msg .= 'Bloqueado por padrão';
}
echo $msg, PHP_EOL;

$msg = 'Definindo USPDEV_IP_CONTROL=localhost: ';
$msg .= localhost('127.0.0.1');
echo $msg . PHP_EOL;

$msg = 'Definindo USPDEV_IP_CONTROL=localhost: ';
$msg .= localhost('127.0.0.2');
echo $msg . PHP_EOL;

$msg = 'Definindo USPDEV_IP_CONTROL=whitelist: ';
$msg .= whitelist('127.0.0.1');
echo $msg . PHP_EOL;

$msg = 'Definindo USPDEV_IP_CONTROL=whitelist: ';
$msg .= whitelist('10.233.0.5');
echo $msg . PHP_EOL;

$msg = 'Definindo USPDEV_IP_CONTROL=whitelist: ';
$msg .= whitelist('143.107.225.6');
echo $msg . PHP_EOL;

// -----------------------------------------------
function localhost($addr)
{
    $_SERVER['REMOTE_ADDR'] = $addr;
    putenv('USPDEV_IP_CONTROL=localhost');

    if (Ipcontrol::proteger(false)) {
        return 'Liberado para ip local ' . $_SERVER['REMOTE_ADDR'];
    } else {
        return 'Bloqueado para ip ' . $_SERVER['REMOTE_ADDR'];
    }
}

function whitelist($addr)
{
    $_SERVER['REMOTE_ADDR'] = $addr;
    putenv('USPDEV_IP_CONTROL=whitelist');
    putenv('USPDEV_IP_CONTROL_FILE=' . __DIR__ . '/allowed_ips.txt');

    if (Ipcontrol::proteger(false)) {
        return 'Liberado para ip ' . $_SERVER['REMOTE_ADDR'];
    } else {
        return 'Bloqueado para ip ' . $_SERVER['REMOTE_ADDR'];
    }
}
