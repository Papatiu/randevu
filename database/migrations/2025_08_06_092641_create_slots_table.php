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
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');
            $table->date('tarih');
            // ESKİ SÜTUNLAR YERİNE DOĞRU SÜTUNU EKLİYORUZ:
            $table->string('saat'); // Örn: "18:00", "19:00" vs.
            $table->integer('kapasite')->default(1); // ileride arttırılabilir
            $table->integer('rezervasyon_sayisi')->default(0); // doluluk kontrolü
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};