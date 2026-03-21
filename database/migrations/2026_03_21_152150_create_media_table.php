<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->morphs('mediable');
            $table->string('disk')->default('public');
            $table->string('original_path');
            $table->string('processed_path')->nullable();
            $table->string('filename');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->boolean('is_cover')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('processing_failed_at')->nullable();
            $table->timestamps();

            $table->index(['mediable_type', 'mediable_id', 'is_cover']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
