<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('classe')->nullable();
            $table->string('gender')->nullable();
            $table->string('photo')->nullable();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->text('cv')->nullable();
            $table->string('address')->nullable();
            $table->date('birth')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('school_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
