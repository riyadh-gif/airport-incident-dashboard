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
     * Incidents (legacy: tb_korban) — airport casualty records.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('condition'); // 'meninggal' | 'ringan' | 'sedang' | 'berat'
            $table->string('hospital');
            $table->date('occurred_at');
            $table->string('location'); // airport gate code, e.g. "A12", "K3"
            $table->string('flight_no');
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
        Schema::dropIfExists('incidents');
    }
};
