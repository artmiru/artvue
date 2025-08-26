<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('about');
            $table->string('phone', 15);
            $table->string('folder', 100);
            $table->string('alt')->nullable();
            $table->string('keypass_code', 255)->comment('Последние 5 цифр магнитного ключа от БЦ Звенигородский');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['id', 'deleted_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('teachers');
    }
}
