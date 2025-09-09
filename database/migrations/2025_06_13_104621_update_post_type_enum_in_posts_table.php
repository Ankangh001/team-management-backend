<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE posts MODIFY post_type ENUM('blog', 'event', 'news') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE posts MODIFY post_type ENUM('blog', 'event') NOT NULL");
    }
};
