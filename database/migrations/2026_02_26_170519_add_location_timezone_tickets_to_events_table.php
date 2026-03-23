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
            $table->dateTime('ends_at')->change();
            $table->string('timezone')->default('UTC')->after('ends_at');
            $table->string('venue_name')->after('timezone');
            $table->string('address')->after('venue_name');
            $table->string('zip', 20)->after('address');
            $table->string('map_url')->nullable()->after('zip');
        });
    }
};
