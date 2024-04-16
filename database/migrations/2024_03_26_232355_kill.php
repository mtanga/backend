<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

   public function up(): void
   {
       Schema::create('kills', function (Blueprint $table) {
           $table->id();
           $table->string('title');
           $table->unsignedInteger('job_id')->nullable();
           $table->timestamps();
           $table->softDeletes();
       });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
       Schema::dropIfExists('kills');
   }
};
