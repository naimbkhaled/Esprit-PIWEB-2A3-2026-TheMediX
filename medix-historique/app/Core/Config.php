<?php
namespace App\Core;

final class Config
{
    private static ?array $cfg = null;
    private static ?array $modules = null;

    public static function get(string $key, $default = null)
    {
        self::$cfg ??= require APP_PATH . '/config/config.php';
        $v = self::$cfg;
        foreach (explode('.', $key) as $part) {
            if (!is_array($v) || !array_key_exists($part, $v)) return $default;
            $v = $v[$part];
        }
        return $v;
    }

    public static function modules(): array
    {
        return self::$modules ??= require APP_PATH . '/config/modules.php';
    }
}
