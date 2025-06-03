<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('streamers', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('login');
            $table->string('display_name');
            $table->string('type');
            $table->string('broadcaster_type');
            $table->text('description');
            $table->string('profile_image_url');
            $table->string('offline_image_url');
            $table->integer('view_count');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streamers');
    }
};
