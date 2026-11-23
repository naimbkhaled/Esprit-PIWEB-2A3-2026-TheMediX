<?php
namespace App\Core;

final class Session
{
    public static function csrf(): string
    {
        return $_SESSION['_csrf'] ??= bin2hex(random_bytes(16));
    }
    public static function csrfValide(string $token): bool
    {
        return hash_equals(self::csrf(), $token);
    }
    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = [$type, $message];
    }
    public static function prendreFlash(): array
    {
        $f = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $f;
    }
}
