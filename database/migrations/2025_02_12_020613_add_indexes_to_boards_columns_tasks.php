<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->index('user_id'); // Optimizes board retrieval by user
            $table->index('deleted_at'); // Improves soft delete queries
        });

        Schema::table('columns', function (Blueprint $table) {
            $table->index(['board_id', 'position']); // Faster sorting & retrieval of columns in a board
            $table->index('deleted_at'); // Soft delete optimization
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['column_id', 'position']); // Faster retrieval & sorting of tasks in a column
            $table->index('due_date'); // Speeds up queries filtering by task deadline
            $table->index('deleted_at'); // Soft delete optimization
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('priority'); // Helps queries filtering tasks by priority
        });
    }

    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('columns', function (Blueprint $table) {
            $table->dropIndex(['board_id', 'position']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['column_id', 'position']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['priority']);
        });
    }
};
