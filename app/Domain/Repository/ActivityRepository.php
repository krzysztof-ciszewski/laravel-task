<?php

namespace App\Domain\Repository;

use App\Domain\ValueObject\ActivityType;
use App\Domain\ValueObject\Airport;
use App\Domain\Entity\Activity;

interface ActivityRepository
{
    /**
     * @var Activity[] $activities
     */
    public function save(array $activities): void;

    /**
     * @return Activity[]
     */
    public function findBy(?\DateTimeImmutable $occurredAtFrom = null, ?\DateTimeImmutable $occurredAtTo = null, ?Airport $location = null, ?ActivityType $type = null): array;
}
