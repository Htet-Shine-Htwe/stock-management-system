<?php

namespace App\Enum;

enum RoleEnum : string
{
    case Admin = 'Admin';
    case User = 'User';

    public static function getRoles(): array
    {
        return [
            self::Admin,
            self::User,
        ];
    }

    public static function getIntValue(string $role): int
    {
        // hardcoded values for demonstration purposes
        return match ($role) {
            self::Admin => 1,
            self::User => 3,
            default => 1,
        };
    }
}
