<?php

declare(strict_types=1);

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
        if (! Schema::hasColumn('categories', 'image_path')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categories', 'image_path')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table): void {
            $table->string('image_path')->nullable()->after('description');
        });
    }
};
