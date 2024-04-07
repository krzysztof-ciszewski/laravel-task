<?php

namespace App\Domain\Service;

use App\Domain\ValueObject\ReportFormat;

interface ActivityParserInterface
{
    /**
     * @return \App\Domain\Entity\Activity[]
     */
    public function parse(string $data, ReportFormat $format): array;
    public function supports(ReportFormat $format): bool;
}
