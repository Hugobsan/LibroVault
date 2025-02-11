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

    //Recebe um array de Roles e verifica se $this é uma delas
    public function isOneOf(array $roles): bool
    {
        return in_array($this, $roles, true);
    }
}