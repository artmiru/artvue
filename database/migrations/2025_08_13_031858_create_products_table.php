<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('type', ['trial', 'regular', 'masterclass', 'material']);
            $table->unsignedInteger('price'); // цена в копейках
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->timestamps();

            // Опционально, для быстрого поиска по типам
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
