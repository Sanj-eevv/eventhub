<?php

declare(strict_types=1);

use App\Enums\OrganizationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('contact_address');
            $table->string('contact_email')->unique();
            $table->string('status')->default(OrganizationStatus::Pending);
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
