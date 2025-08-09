<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_status_to_sports_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('resim');
            $table->string('status_reason')->nullable()->after('is_active');
            $table->string('location_coordinates')->nullable()->after('status_reason');
        });
    }

    public function down(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'status_reason', 'location_coordinates']);
        });
    }
};