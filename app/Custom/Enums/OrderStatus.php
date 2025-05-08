<?php

namespace App\Custom\Enums;

enum OrderStatus: string implements Enum
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    /**
     * Returns all cases' values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
