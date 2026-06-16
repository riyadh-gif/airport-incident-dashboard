<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Flights (legacy: tb_maskapai) — airline/flight records tied to a gate.
     */
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_no'); // e.g. "GA-431"
            $table->date('occurred_at');
            $table->string('location'); // airport gate code
            $table->timestamps();

            $table->index('occurred_at');
            $table->index('location');
            $table->index('flight_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
