<?php

use App\Domain\ValueObject\ActivityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->enum('type', array_column(ActivityType::cases(), 'value'));
            $table->dateTime('occurred_at');
            $table->char('location', 3);
            $table->char('to',3)->nullable();
            $table->dateTime('scheduled_time_departure')->nullable();
            $table->dateTime('scheduled_time_arrival')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
