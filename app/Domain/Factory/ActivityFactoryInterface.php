<?php

namespace App\Domain\Factory;

use App\Domain\Entity\Activity;
use App\Domain\ValueObject\ActivityType;
use App\Domain\ValueObject\Airport;

interface ActivityFactoryInterface
{
    public function create(ActivityType $type, Airport $location, \DateTimeImmutable $occurredAt, Airport $to = null, \DateTimeImmutable $scheduledTimeDeparture = null, \DateTimeImmutable $scheduledTimeArrival = null): Activity;
}
