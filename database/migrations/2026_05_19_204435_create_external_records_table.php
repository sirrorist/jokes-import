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
        Schema::create('external_records', function (Blueprint $table) {
            $table->id();
            $table->string('source', 64);
            $table->string('external_id', 64);
            $table->string('record_hash', 64)->nullable();
            $table->string('type', 32)->nullable();
            $table->text('setup')->nullable();
            $table->text('punchline')->nullable();
            $table->json('payload_json');
            $table->timestamps();

            $table->unique(['source', 'external_id']);
            $table->index('source');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_records');
    }
};
