<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('ticket_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('booking_reference')->unique();
            $table->string('attendee_name');
            $table->string('attendee_email');
            $table->string('status')->default('pending');
            $table->index(['event_id', 'status']);
            $table->string('qr_code_path')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
