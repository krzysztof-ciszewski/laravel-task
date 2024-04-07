<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\ValueObject\ActivityType;
use App\Domain\ValueObject\Airport;

/**
 * @property int $id
 * @property ActivityType $type
 * @property \DateTimeImmutable $occurred_at
 * @property Airport $location
 * @property Airport $to
 * @property \DateTimeImmutable $scheduled_time_departure
 * @property \DateTimeImmutable $scheduled_time_arrival
 */
class Activity extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['type', 'occurred_at', 'location', 'to', 'scheduled_time_departure', 'scheduled_time_arrival'];

    protected $hidden = ['updated_at', 'created_at'];

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ActivityType::tryFrom($value),
            set: fn (ActivityType $type) => $type->value,
        );
    }

    protected function location(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => new Airport($value),
            set: fn (Airport $airport) => $airport->code,
        );
    }

    protected function to(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value !== null ? new Airport($value) : null,
            set: fn (?Airport $airport) => $airport->code ?? null,
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurred_at' => 'immutable_datetime',
            'scheduled_time_departure' => 'immutable_datetime',
            'scheduled_time_arrival' => 'immutable_datetime',
        ];
    }
}
