<?php

namespace App\Domain\ValueObject;

enum ActivityType: string
{
    case DayOff = 'DO';
    case StandBy = 'SBY';
    case Flight = 'FLT';
    case CheckIn = 'CI';
    case CheckOut = 'CO';
    case Unknown = 'UNK';
}
