<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('portal_id')->after('id'); // Add portal_id column
            $table->unsignedBigInteger('user_role_id')->after('portal_id'); // Add user_role_id column

            // Add foreign key constraints
            $table->foreign('portal_id')->references('id')->on('portals')->onDelete('cascade');
            $table->foreign('user_role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['portal_id']);
            $table->dropForeign(['user_role_id']);
            $table->dropColumn('portal_id');
            $table->dropColumn('user_role_id');
        });
    }
};
