<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        DB::table('files')
            ->whereNull('uuid')
            ->orderBy('id')
            ->each(function (object $file): void {
                DB::table('files')
                    ->where('id', $file->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table): void {
            $table->dropUnique('files_uuid_unique');
            $table->dropColumn('uuid');
        });
    }
};
