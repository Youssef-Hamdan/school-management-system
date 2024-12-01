<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyGradeAndAssessmentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('percentage'); // Drop the percentage column from the grades table
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->after('type')->nullable(); // Add percentage to the assessments table
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('percentage'); // Drop the percentage column from the assessments table
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->after('grade')->nullable(); // Add percentage back to the grades table
        });
    }
}
