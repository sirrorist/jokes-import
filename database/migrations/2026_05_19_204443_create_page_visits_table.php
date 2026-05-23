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
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_hash', 64);
            $table->string('ip', 45);
            $table->string('city', 128)->nullable();
            $table->string('device_type', 32);
            $table->text('user_agent');
            $table->text('url');
            $table->timestamp('visited_at');
            $table->timestamp('hour_bucket');
            $table->boolean('is_unique_in_hour')->default(true);
            $table->timestamps();

            $table->index(['hour_bucket', 'is_unique_in_hour']);
            $table->index('city');
            $table->index(['visitor_hash', 'hour_bucket']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
