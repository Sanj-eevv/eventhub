<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dateTime('ends_at')->nullable()->change();
            $table->string('timezone')->default('UTC')->after('ends_at');
            $table->json('location')->nullable()->after('timezone');
        });
    }
};
