<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banned_tcs', function (Blueprint $table) {
            $table->id();
            $table->string('tc_kimlik', 11)->unique(); // Banlanan TC
            $table->timestamp('ban_bitis_tarihi'); // Ban'ın ne zaman biteceği
            $table->text('sebep')->nullable(); // Neden banlandığı (örn: Randevu #123'e gelmedi)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_tcs');
    }
};
