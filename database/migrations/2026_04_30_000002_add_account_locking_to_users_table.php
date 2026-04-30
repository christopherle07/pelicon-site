<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->timestamp('locked_at')->nullable()->after('forum_notifications_enabled');
            $table->foreignId('locked_by_user_id')
                ->nullable()
                ->after('locked_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('locked_by_user_id');
            $table->dropColumn('locked_at');
        });
    }
};
