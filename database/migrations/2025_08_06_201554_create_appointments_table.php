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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained('slots')->onDelete('cascade');

            // Misafir Kullanıcı Bilgileri
            $table->string('tc_kimlik', 11);
            $table->string('ad');
            $table->string('soyad');
            $table->string('dogum_yili');
            $table->string('telefon');

            // Randevu Bilgileri
            $table->string('iptal_kodu', 10)->unique(); // Rastgele oluşturulacak iptal kodu
            $table->string('durum')->default('onay_bekliyor'); // onay_bekliyor, onaylandi, iptal_edildi, tamamlandi, gelmedi

            // Hangi kullanıcı (admin) işlem yaptı?
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Sadece admin paneli işlemleri için

            $table->text('aciklama')->nullable(); // Admin veya job'ların not bırakması için

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
