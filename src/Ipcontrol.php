<?php namespace Uspdev\Ipcontrol;

class Ipcontrol
{
    // o nome do arquivo é predefinido
    const ip_white_file = 'uspdev_ip_control_whitelist.txt';

    public static function proteger($die = true)
    {

        $ipControl = getenv('USPDEV_IP_CONTROL');

        $ret = false;
        switch ($ipControl) {
            case false:
            case '0':
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
                    if ($local = getenv('USPDEV_IP_CONTROL_LOCAL')) {
                        $whitefile = $local . '/' . SELF::ip_white_file;
                    } else {
                        die('USPDEV_IP_CONTROL_LOCAL não definido!');
                    }
                    $whitelist = SELF::leArquivo($whitefile);
                    $ret = SELF::whiteList($whitelist);
                }
                break;
        }

        // se for negar acesso vamos verificar se vai ser 403 ou false
        if ($ret == false && $die == true) {
            header('HTTP/1.0 403 Forbidden');
            die($msg);
        }
        return $ret;
    }

    public static function status()
    {
        $ip_control = getenv('USPDEV_IP_CONTROL'); // ? 'não definido' : getenv('USPDEV_IP_CONTROL');

        $ret['enable'] = $ip_control ? 'sim' : 'não'; // getenv('USPDEV_IP_CONTROL'); // ? 'não definido' : getenv('USPDEV_IP_CONTROL');
        if ($ip_control == 'whitelist') {
            $ret['ip_control'] = $ip_control;
            $ret['local'] = getenv('USPDEV_IP_CONTROL_LOCAL');
            $ret['ip_whitelist'] = SELF::leArquivo(getenv('USPDEV_IP_CONTROL_LOCAL') . '/' . SELF::ip_white_file);
        }

        return $ret;
    }

    public static function leArquivo($csv_file)
    {
        // vamos ler o arquivo de endereços autorizados
        if (($handle = @fopen($csv_file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $iplist[] = $row;
            }
            fclose($handle);
            return $iplist;
        } else {
            die('Erro fatal ao ler arquivo ' . $csv_file);
        }
    }

    protected static function whiteList($iplist)
    {
        // Vamos percorrer a lista e testar uma um
        foreach ($iplist as $ip) {

            // https://stackoverflow.com/questions/2869893/block-specific-ip-block-from-my-website-in-php
            $network = ip2long($ip[0]);
            $prefix = (int) $ip[1];
            $remote_ip = ip2long($_SERVER['REMOTE_ADDR']);

            if ($network >> (32 - $prefix) == $remote_ip >> (32 - $prefix)) {
                // Se sim, vamos liberar o acesso
                return true;
            }
            // se não, vamos para o próximo
        }
        return false;
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
