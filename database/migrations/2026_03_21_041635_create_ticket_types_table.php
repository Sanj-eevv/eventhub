<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('capacity');
            $table->unsignedSmallInteger('max_per_user')->default(5);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('sale_starts_at')->nullable();
            $table->timestamp('sale_ends_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
