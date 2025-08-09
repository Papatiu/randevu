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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Duyuru Başlığı
            $table->text('content'); // Duyuru İçeriği
            $table->boolean('is_active')->default(true); // Duyuru aktif mi?
            $table->timestamp('show_until')->nullable(); // Opsiyonel: Bu tarihe kadar göster
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
