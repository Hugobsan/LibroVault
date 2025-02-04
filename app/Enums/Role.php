<?php
namespace App\Enums;
enum Role: string
{
    case ADMIN = 'admin';
    case STANDARD = 'standard';
    case PLUS = 'plus';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}