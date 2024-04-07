<?php

namespace App\Domain\Factory;

use App\Domain\Entity\Activity;
use App\Domain\ValueObject\ActivityType;
use App\Domain\ValueObject\Airport;

class ActivityFactory implements ActivityFactoryInterface
{
    public function create(ActivityType $type, Airport $location, \DateTimeImmutable $occurredAt, Airport $to = null, \DateTimeImmutable $scheduledTimeDeparture = null, \DateTimeImmutable $scheduledTimeArrival = null): Activity
    {
        $activity = new Activity();
        $activity->type = $type;
        $activity->location = $location;
        $activity->to = $to;
        $activity->occurred_at = $occurredAt;
        $activity->scheduled_time_departure = $scheduledTimeDeparture;
        $activity->scheduled_time_arrival = $scheduledTimeArrival;

        return $activity;
    }
}
