<?php

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
        Schema::table('reports', function (Blueprint $table) {
            $table->index(['vote_score', 'created_at'], 'reports_vote_score_created_at_idx');
            $table->index(['status', 'vote_score'], 'reports_status_vote_score_idx');
            $table->index(['rank_tier', 'vote_score'], 'reports_rank_tier_vote_score_idx');
            $table->index(['user_id', 'created_at'], 'reports_user_id_created_at_idx');
            $table->index(['latitude', 'longitude', 'deleted_at'], 'reports_lat_lng_deleted_at_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('is_verified', 'users_is_verified_idx');
            $table->index('is_admin', 'users_is_admin_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'notifications_notifiable_read_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_vote_score_created_at_idx');
            $table->dropIndex('reports_status_vote_score_idx');
            $table->dropIndex('reports_rank_tier_vote_score_idx');
            $table->dropIndex('reports_user_id_created_at_idx');
            $table->dropIndex('reports_lat_lng_deleted_at_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_is_verified_idx');
            $table->dropIndex('users_is_admin_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_read_at_idx');
        });
    }
};
