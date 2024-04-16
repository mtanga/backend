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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('presentation')->nullable();
            $table->double('salary')->nullable();
            $table->integer('places');
            $table->double('hours')->nullable();
            $table->string('code')->nullable();
            $table->dateTime('start', precision: 0);
            $table->dateTime('end', precision: 0);
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
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
        Schema::dropIfExists('jobs');
    }
};
