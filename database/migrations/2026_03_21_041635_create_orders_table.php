<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('event_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('reserved');
            $table->char('currency', 3)->default('USD');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('total');
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_client_secret')->nullable();
            $table->string('stripe_refund_id')->nullable();
            $table->string('refund_status')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
