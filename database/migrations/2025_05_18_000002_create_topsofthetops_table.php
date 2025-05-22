<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('topsofthetops', function (Blueprint $table) {
            $table->id();
            $table->string('game_id');
            $table->string('game_name');
            $table->string('user_name');
            $table->integer('total_videos');
            $table->bigInteger('total_views');
            $table->string('mv_title');
            $table->bigInteger('mv_views');
            $table->string('mv_duration');
            $table->timestamp('mv_created_at');
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topsofthetops');
    }
};
