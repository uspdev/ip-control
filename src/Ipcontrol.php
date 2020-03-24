<?php namespace Uspdev\Ipcontrol;

class Ipcontrol
{
    public static function proteger($die = true)
    {

        $ipControl = getenv('USPDEV_IP_CONTROL');

        $ret = false;
        switch ($ipControl) {
            case false:
                // variavel nao foi definida, vamos liberar
                $ret = true;
                break;

            case 'localhost':
                $ret = SELF::localhost();
                break;

            case 'whitelist':
                if (SELF::localhost()) {
                    $ret = true;
                } else {
                    $ipfile = getenv('USPDEV_IP_CONTROL_FILE');
                    if (empty($ipfile)) {
                        die('USPDEV_IP_CONTROL_FILE não definido!');
                    }
                    $ret = SELF::whitelist($ipfile);
                }
                break;
        }

        // se for negar acesso vamos verificar se vai ser 403 ou false
        if ($ret == false) {
            if ($die == true) {
                SELF::halt();
            }
        }
        return $ret;
    }

    public static function status()
    {
        $ret['USPDEV_IP_CONTROL'] = getenv('USPDEV_IP_CONTROL');// ? 'não definido' : getenv('USPDEV_IP_CONTROL');
        if ($ret['USPDEV_IP_CONTROL'] == 'whitelist') {
            //$ret['USPDEV_IP_CONTROL_FILE'] = getenv('USPDEV_IP_CONTROL_FILE');
            $ret['ip_list'] = SELF::getIpList(getenv('USPDEV_IP_CONTROL_FILE'));
        }
        
        return $ret;
    }

    public static function getIpList($ipfile)
    {
        // vamos ler o arquivo de endereços autorizados
        if (($handle = fopen($ipfile, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $iplist[] = $row;
            }
            fclose($handle);
            return $iplist;
        }
    }

    protected static function halt($msg = '')
    {
        header('HTTP/1.0 403 Forbidden');
        die($msg);
    }

    protected static function whiteList($ipfile)
    {
        // vamos ler o arquivo de endereços autorizados
        if (($handle = fopen($ipfile, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {

                // e ver se o ip está na lista
                // https://stackoverflow.com/questions/2869893/block-specific-ip-block-from-my-website-in-php
                $network = ip2long($row[0]);
                $prefix = (int) $row[1];
                $ip = ip2long($_SERVER['REMOTE_ADDR']);

                if ($network >> (32 - $prefix) == $ip >> (32 - $prefix)) {
                    // Se sim, vamos liberar o acesso
                    fclose($handle);
                    return true;
                }
            }
            fclose($handle);
            // aqui vamos negar o acesso
            return false;
        } else {
            die('Erro fatal ao ler arquivo ' . $ipfile);
        }
    }

    protected static function localhost()
    {
        if ($_SERVER['REMOTE_ADDR'] == '::1' ||
            $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ||
            $_SERVER['REMOTE_ADDR'] == '127.0.1.1') {
            return true;
        } else {
            return false;
        }
    }
}
