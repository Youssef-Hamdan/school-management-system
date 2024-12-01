<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('student_id');
            $table->integer('grade');
            $table->decimal('percentage', 5, 2);
            $table->boolean('is_done')->default(false);
            $table->timestamps();
            $table->softDeletes(); // For deleted_at

            // Foreign Keys
            $table->foreign('assessment_id')->references('id')->on('assessments');
            $table->foreign('student_id')->references('id')->on('users'); // Adjust table name
        });
    }
};
