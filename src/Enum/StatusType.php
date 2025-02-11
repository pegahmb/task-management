<?php

namespace App\Enum;

enum StatusType: string
{
    case OPEN = 'Open';
    case IN_PROGRESS = 'In progress';
    case REVIEW = 'Review';
    case DONE = 'Done';
    case CLOSED = 'Closed';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
