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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Konten Laporan
            $table->string('title');
            $table->text('description');

            // Foto Base64 (Kompresi 80%)
            $table->mediumText('image_base64');

            // Koordinat Geografis (untuk Heatmap & Peta)
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('geohash', 12)->nullable()->index();

            // Wilayah dari OpenFreeMap (Reverse Geocoding)
            $table->string('province')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('district')->nullable()->index();
            $table->string('subdistrict')->nullable()->index();
            $table->text('formatted_address')->nullable();
            $table->string('osm_place_id')->nullable();

            // Gamifikasi & Tier Postingan
            $table->enum('rank_tier', ['normal', 'trending', 'urgent', 'critical'])
                ->default('normal')
                ->index();

            // Counter Cache Voting
            $table->integer('upvotes_count')->default(0);
            $table->integer('downvotes_count')->default(0);
            $table->integer('vote_score')->default(0)->index();
            $table->unsignedInteger('comments_count')->default(0);

            // Status Laporan Komunitas
            $table->enum('status', ['active', 'in_progress', 'resolved', 'archived'])
                ->default('active')
                ->index();

            $table->timestamps();
            $table->softDeletes();

            // Index performa query
            $table->index(['latitude', 'longitude']);
            $table->index(['city', 'vote_score']);
            $table->index(['district', 'vote_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
