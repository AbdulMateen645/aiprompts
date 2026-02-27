<?php

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
        Schema::table('prompts', function (Blueprint $table) {
            $table->enum('pricing_type', ['free', 'paid', 'premium'])->default('free')->after('is_featured');
            $table->decimal('price', 8, 2)->default(0.00)->after('pricing_type');
            $table->boolean('is_premium')->default(false)->after('price');
            $table->enum('access_level', ['public', 'subscriber', 'premium'])->default('public')->after('is_premium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropColumn(['pricing_type', 'price', 'is_premium', 'access_level']);
        });
    }
};
