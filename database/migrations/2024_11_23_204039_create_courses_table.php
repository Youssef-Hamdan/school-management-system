<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_name');
            $table->unsignedBigInteger('instructor_id');
            $table->boolean('is_online');
            $table->unsignedBigInteger('schedule_id');
            $table->timestamps();
            $table->softDeletes(); 
    
            // Foreign Keys
            $table->foreign('instructor_id')->references('id')->on('users'); // Adjust table name
            $table->foreign('schedule_id')->references('id')->on('schedules');
        });
    }
    
    
};
