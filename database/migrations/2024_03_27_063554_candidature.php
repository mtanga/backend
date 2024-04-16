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
            Schema::create('candidatures', function (Blueprint $table) {
                $table->id();
                $table->string('status');
                $table->unsignedInteger('student_id')->nullable();
                $table->unsignedInteger('job_id')->nullable();
                $table->unsignedInteger('enterprise_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
     
        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('candidatures');
        }
};
