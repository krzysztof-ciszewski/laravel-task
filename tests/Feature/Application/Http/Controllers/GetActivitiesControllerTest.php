<?php

namespace Feature\Application\Http\Controllers;

use App\Domain\Entity\Activity;
use App\Domain\ValueObject\ActivityType;
use App\Domain\ValueObject\Airport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GetActivitiesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testInvalidType(): void
    {
        $response = $this->get('api/activity?type=ASD');

        $response->assertStatus(400);
        $response->assertJson(['type' => ['The selected type is invalid.']]);
    }

    public function testInvalidLocation(): void
    {
        $response = $this->get('api/activity?location=ASD3');

        $response->assertStatus(400);
        $response->assertJson(['location' => ['The location field must not be greater than 3 characters.', 'The location field format is invalid.']]);
    }

    public function testInvalidOccurredAtFrom(): void
    {
        $response = $this->get('api/activity?occurred_at_from=aaa');

        $response->assertStatus(400);
        $response->assertJson(['occurred_at_from' => ['The occurred at from field must be a valid date.', 'The occurred at from field must match the format d-m-Y.']]);
    }

    public function testInvalidOccurredAtTo(): void
    {
        $response = $this->get('api/activity?occurred_at_to=aaa');

        $response->assertStatus(400);
        $response->assertJson(['occurred_at_to' => ['The occurred at to field must be a valid date.', 'The occurred at to field must match the format d-m-Y.']]);
    }

    #[DataProvider('getEventsProvider')]
    public function testGetEvents(array $activities, array $filters, array $expectedActivites): void
    {
        foreach ($activities as $activity) {
            $this->createActivity($activity['type'], $activity['location'], $activity['occurred_at'], $activity['to'] ?? null, $activity['scheduled_time_departure'] ?? null, $activity['scheduled_time_arrival'] ?? null)->save();
        }

        $response = $this->get('api/activity?' . http_build_query($filters));

        $response->assertStatus(200);
        $result = json_decode($response->getContent(), true);

        self::assertCount(count($expectedActivites), $result);

        usort($result, static fn (array $a, array $b) => new \DateTimeImmutable($a['occurred_at']) <=> new \DateTimeImmutable($b['occurred_at']));
        usort($expectedActivites, static fn (array $a, array $b) => $a['occurred_at'] <=> $b['occurred_at']);

        foreach ($expectedActivites as $index => $activity) {
            self::assertSame($activity['type']->value, $result[$index]['type']);
            self::assertSame($activity['occurred_at']->format('Y-m-d H:i:s'), $result[$index]['occurred_at']);
            self::assertSame($activity['location']->code, $result[$index]['location']['code']);

            if (isset($activity['to'])) {
                self::assertSame($activity['to']->code, $result[$index]['to']['code']);
            }

            if (isset($activity['scheduled_time_departure'])) {
                self::assertSame($activity['scheduled_time_departure']->format('Y-m-d H:i:s'), $result[$index]['scheduled_time_departure']);
            }

            if (isset($activity['scheduled_time_arrival'])) {
                self::assertSame($activity['scheduled_time_arrival']->format('Y-m-d H:i:s'), $result[$index]['scheduled_time_arrival']);
            }
        }
    }

    public static function getEventsProvider(): array
    {
        return [
            [
                [
                    ['type' => ActivityType::DayOff, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('01-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckOut, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('03-01-2020 00:00:00')],
                ],
                [
                    'type' => ActivityType::DayOff->value,
                ],
                [
                    ['type' => ActivityType::DayOff, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('01-01-2020 00:00:00')]
                ]
            ],
            [
                [
                    ['type' => ActivityType::DayOff, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('01-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckOut, 'location' => new Airport('KRP'), 'occurred_at' => new \DateTimeImmutable('03-01-2020 00:00:00')],
                ],
                [
                    'location' => 'CPH',
                ],
                [
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                ]
            ],
            [
                [
                    ['type' => ActivityType::DayOff, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('01-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckOut, 'location' => new Airport('KRP'), 'occurred_at' => new \DateTimeImmutable('03-01-2020 00:00:00')],
                ],
                [
                    'occurred_at_from' => '02-01-2020',
                ],
                [
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckOut, 'location' => new Airport('KRP'), 'occurred_at' => new \DateTimeImmutable('03-01-2020 00:00:00')],
                ]
            ],
            [
                [
                    ['type' => ActivityType::DayOff, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('01-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckOut, 'location' => new Airport('KRP'), 'occurred_at' => new \DateTimeImmutable('03-01-2020 00:00:00')],
                ],
                [
                    'occurred_at_to' => '02-01-2020',
                ],
                [
                    ['type' => ActivityType::DayOff, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('01-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                ]
            ],
            [
                [
                    ['type' => ActivityType::DayOff, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('01-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('03-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckOut, 'location' => new Airport('KRP'), 'occurred_at' => new \DateTimeImmutable('04-01-2020 00:00:00')],
                ],
                [
                    'occurred_at_from' => '02-01-2020',
                    'occurred_at_to' => '03-01-2020',
                ],
                [
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('03-01-2020 00:00:00')],
                ]
            ],
            [
                [
                    ['type' => ActivityType::DayOff, 'location' => new Airport('WAW'), 'occurred_at' => new \DateTimeImmutable('01-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckIn, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('02-01-2020 00:00:00')],
                    ['type' => ActivityType::Flight, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('03-01-2020 00:00:00')],
                    ['type' => ActivityType::CheckOut, 'location' => new Airport('KRP'), 'occurred_at' => new \DateTimeImmutable('04-01-2020 00:00:00')],
                ],
                [
                    'occurred_at_from' => '02-01-2020',
                    'occurred_at_to' => '03-01-2020',
                    'type' => ActivityType::Flight->value,
                    'location' => 'CPH'
                ],
                [
                    ['type' => ActivityType::Flight, 'location' => new Airport('CPH'), 'occurred_at' => new \DateTimeImmutable('03-01-2020 00:00:00')],
                ]
            ],
        ];
    }

    private function createActivity(ActivityType $type, Airport $location, \DateTimeImmutable $occurredAt, ?Airport $to = null, ?\DateTimeImmutable $scheduledTimeDeparture = null, ?\DateTimeImmutable $scheduledTimeArrival = null): Activity
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
