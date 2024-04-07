<?php

namespace App\Infrastructure\Database;

use App\Domain\Entity\Activity;
use App\Domain\ValueObject\ActivityType;
use App\Domain\ValueObject\Airport;

class ActivityRepository implements \App\Domain\Repository\ActivityRepository
{
    /**
     * @inheritDoc
     */
    public function save(array $activities): void
    {
        //TODO: refactor into one query
        foreach ($activities as $activity) {
            $activity->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function findBy(?\DateTimeImmutable $occurredAtFrom = null, ?\DateTimeImmutable $occurredAtTo = null, ?Airport $location = null, ?ActivityType $type = null): array
    {
        $query = Activity::query();

        if ($occurredAtFrom !== null) {
            $query->where('occurred_at', '>=', $occurredAtFrom->format('Y-m-d 00:00:00'));
        }

        if ($occurredAtTo !== null) {
            $query->where('occurred_at', '<=', $occurredAtTo->format('Y-m-d 23:59:59'));
        }

        if ($location !== null) {
            $query->where('location', $location->code);
        }

        if ($type !== null) {
            $query->where('type', $type);
        }
        return $query->get()->toArray();
    }
}
