<?php

use App\Models\User;
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
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('location', 140)->index();
            $table->timestamp('date_timestamp');
            $table->string('date_txt', 20);
            $table->float('temp_max');
            $table->float('temp_min');
            $table->string('weather', 140)->index();
            $table->string('weather_description', 140)->index();
            $table->string('weather_icon', 20)->index();
            $table->longText('forecasts');
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};
