<?php

namespace App\Enums;

enum Genre: string
{
    case FICTION = 'fiction';
    case NON_FICTION = 'non-fiction';
    case MYSTERY = 'mystery';
    case FANTASY = 'fantasy';
    case SCI_FI = 'sci-fi';
    case BIOGRAPHY = 'biography';
    case HISTORY = 'history';
    case ROMANCE = 'romance';
    case SELF_HELP = 'self-help';
    case OTHER = 'other';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

