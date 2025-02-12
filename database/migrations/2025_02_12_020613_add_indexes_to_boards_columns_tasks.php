<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->index('user_id'); // Improves queries filtering boards by user
            $table->index('deleted_at'); // Enhances soft delete performance
        });

        Schema::table('columns', function (Blueprint $table) {
            $table->index('board_id'); // Optimizes queries retrieving columns for a board
            $table->index('position'); // Improves sorting columns by position
            $table->index('deleted_at'); // Enhances soft delete performance
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('column_id'); // Optimizes queries retrieving tasks for a column
            $table->index('position'); // Improves sorting tasks by position within a column
            $table->index('due_date'); // Speeds up queries filtering tasks by due date
            $table->index('deleted_at'); // Enhances soft delete performance
        });
    }

    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('columns', function (Blueprint $table) {
            $table->dropIndex(['board_id']);
            $table->dropIndex(['position']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['column_id']);
            $table->dropIndex(['position']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['deleted_at']);
        });
    }
};
