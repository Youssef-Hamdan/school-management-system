<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_active')->default(false)->change();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_active')->change(); // Remove the default if rolling back
    });
}

};
