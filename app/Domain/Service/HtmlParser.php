<?php

namespace App\Domain\Service;

use App\Domain\Factory\ActivityFactoryInterface;
use App\Domain\ValueObject\ActivityType;
use App\Domain\ValueObject\Airport;
use App\Domain\ValueObject\ReportFormat;
use Symfony\Component\DomCrawler\Crawler;

class HtmlParser implements ActivityParserInterface
{
    private const DATE_INDEX = 1;
    private const CHECK_IN_INDEX = 5;
    private const CHECK_OUT_INDEX = 7;
    private const ACTIVITY_INDEX = 8;
    private const FROM_INDEX = 11;
    private const TO_INDEX = 15;
    private const STD_INDEX = 13;
    private const STA_INDEX = 17;

    private const TABLE_ID = '#ctl00_Main_activityGrid';

    public function __construct(
        private readonly ActivityFactoryInterface $activityFactory,
    ) {
    }

    public function parse(string $data, ReportFormat $format): array
    {
        if (!$this->supports($format)) {
            throw new \InvalidArgumentException(sprintf('Expected format %s, got %s', ReportFormat::HTML->value, $format->value));
        }

        return $this->createActivities($data);
    }

    public function supports(ReportFormat $format): bool
    {
        return $format === ReportFormat::HTML;
    }

    private function createActivities(string $data): array
    {
        if (empty($data)) {
            return [];
        }

        $crawler = new Crawler($data);

        $this->validate($crawler);

        $dateText = $crawler->filter('.printOnly')->text();
        $matches = [];
        preg_match('/Period: (\d{2}\w{3}\d{2})/', $dateText, $matches);
        $dateFrom = \DateTimeImmutable::createFromFormat('dMy', $matches[1]);
        $dateFrom = $dateFrom->setTime(0, 0, 0);


        $table = $crawler->filter(self::TABLE_ID)->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        array_shift($table);

        $activities = [];
        $date = $dateFrom;
        foreach ($table as $row) {
            if (!empty($row[self::DATE_INDEX])) {
                $matches = [];
                preg_match('/\w{3} (\d{2})/', $row[self::DATE_INDEX], $matches);
                $day = $matches[1];
                $date = $dateFrom->setDate((int) $dateFrom->format('Y'), (int) $dateFrom->format('m'), (int) $day);
            }


            $matches = [];
            preg_match('/(\d{2})(\d{2})/', $row[self::CHECK_IN_INDEX], $matches);
            if (!empty($matches[1])) {
                $activities[] = $this->activityFactory->create(ActivityType::CheckIn, new Airport($row[self::TO_INDEX]), $date->setTime((int) $matches[1], (int) $matches[2], 0));
            }

            $matches = [];
            preg_match('/(\d{2})(\d{2})/', $row[self::CHECK_OUT_INDEX], $matches);
            if (!empty($matches[1])) {
                $activities[] = $this->activityFactory->create(ActivityType::CheckOut, new Airport($row[self::FROM_INDEX]), $date->setTime((int) $matches[1], (int) $matches[2], 0));
            }

            if ($this->isFlight($row[self::ACTIVITY_INDEX])) {
                $activities[] = $this->activityFactory->create(ActivityType::Flight, new Airport($row[self::FROM_INDEX]), $date, new Airport($row[self::TO_INDEX]), $this->getSTD($date, $row[self::STD_INDEX]), $this->getSTA($date, $row[self::STA_INDEX]));
            }

            if ($this->isDayOff($row[self::ACTIVITY_INDEX])) {
                $activities[] = $this->activityFactory->create(ActivityType::DayOff, new Airport($row[self::FROM_INDEX]), $date);
            }

            if ($this->isStandBy($row[self::ACTIVITY_INDEX])) {
                $activities[] = $this->activityFactory->create(ActivityType::StandBy, new Airport($row[self::FROM_INDEX]), $date);
            }

            if ($this->isUnknown($row[self::ACTIVITY_INDEX])) {
                $activities[] = $this->activityFactory->create(ActivityType::Unknown, new Airport($row[self::FROM_INDEX]), $date);
            }
        }

        return $activities;
    }

    private function getSTD(\DateTimeImmutable $date, string $value): \DateTimeImmutable
    {
        $matches = [];
        preg_match('/(\d{2})(\d{2})/', $value, $matches);

        return $date->setTime($matches[1], $matches[2]);
    }

    private function getSTA(\DateTimeImmutable $date, string $value): \DateTimeImmutable
    {
        $matches = [];
        preg_match('/(\d{2})(\d{2})/', $value, $matches);

        return $date->setTime($matches[1], $matches[2]);
    }

    private function isFlight(string $value): bool
    {
        $matches = [];
        preg_match('/^(\w{2}\d+)$/', $value, $matches);

        return !empty($matches[1]);
    }

    private function isDayOff(string $value): bool
    {
        return strstr($value, 'OFF');
    }

    private function isStandBy(string $value): bool
    {
        return strstr($value, 'SBY');
    }

    private function isUnknown(string $value): bool
    {
        return !$this->isFlight($value) && !$this->isDayOff($value) && !$this->isStandBy($value);
    }

    private function validate(Crawler $crawler): void
    {
        if ($crawler->filter('.printOnly')->count() !== 1 || $crawler->filter(self::TABLE_ID)->count() !== 1 && $crawler->filter(self::TABLE_ID)->filter('tr')->count() <= 1) {
            throw new \InvalidArgumentException('Invalid report');
        }
    }
}
