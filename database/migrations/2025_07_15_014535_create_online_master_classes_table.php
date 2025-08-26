<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('online_master_classes', function (Blueprint $table) {
            $table->id();
            $table->string('img');
            $table->string('title');
            $table->tinyInteger('level');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->unsignedInteger('price'); // цена в копейках
            $table->string('link');
            $table->string('paid_link');
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->json('colors');
            $table->json('materials');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('online_master_classes');
    }
};
