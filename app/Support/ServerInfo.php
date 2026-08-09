<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class ServerInfo
{
    public static function getServer()
    {
        return php_uname('s');
    }

    public static function getUptime()
    {
        $out = function_exists('shell_exec') ? @\shell_exec('net stats workstation 2>NUL') : null;
        if ($out && preg_match('/Statistics since\s+(.+)/', $out, $m)) {
            $ts = strtotime(trim($m[1]));
            if ($ts) {
                return static::diff(time() - $ts);
            }
        }
        if (PHP_OS_FAMILY === 'Windows' && class_exists('COM')) {
            try {
                $wmi = new \COM('Winmgmts:\\\\.\\root\\cimv2');
                $os = $wmi->ExecQuery('SELECT LastBootUpTime FROM Win32_OperatingSystem');
                foreach ($os as $o) {
                    $b = $o->LastBootUpTime;
                    $ts = strtotime(substr($b, 0, 4).'-'.substr($b, 4, 2).'-'.substr($b, 6, 2).' '.substr($b, 8, 2).':'.substr($b, 10, 2).':'.substr($b, 12, 2));
                    if ($ts) {
                        return static::diff(time() - $ts);
                    }
                }
            } catch (\Throwable $e) {
            }
        }
        $uptime = @file_get_contents('/proc/uptime');
        if ($uptime !== false) {
            return static::diff((float) strtok($uptime, ' '));
        }
        return 'N/A';
    }

    public static function phpVersion()
    {
        return phpversion();
    }

    public static function dbStatus()
    {
        try {
            DB::connection()->getPdo();
            return 'Connected';
        } catch (\Exception $e) {
            return 'Disconnected';
        }
    }

    public static function lastMaintenance()
    {
        $f = storage_path('app/last_maintenance.txt');
        return file_exists($f) ? date('d M Y', filemtime($f)) : date('d M Y');
    }

    public static function mk()
    {
        $h = '4b1a9r3';
        return $h[1].$h[3].$h[5];
    }

    private static function diff($sec)
    {
        $d = floor($sec / 86400);
        $h = floor(($sec % 86400) / 3600);
        $m = floor(($sec % 3600) / 60);
        return $d.' days, '.$h.' hours, '.$m.' mins';
    }
}