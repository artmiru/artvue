<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_class_bookings', function (Blueprint $table) {
            // Идентификатор
            $table->id();

            // Связи
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_class_id')->constrained('master_classes')->cascadeOnDelete();
            $table->foreignId('gift_certificate_id')->nullable()->constrained('gift_certificates');

            // Статусы
            $table->enum('payment_status', [
                'pending',       // Ожидает оплаты
                'paid',          // Оплачено
                'failed',        // Ошибка оплаты
                'refunded',      // Возврат
                'partially_refunded' // Частичный возврат
            ])->default('pending');

            $table->enum('visit_status', [
                'pending',       // Ожидает посещения
                'visited',       // Посещено
                'no_show',       // Не пришел
                'cancelled',     // Отменено клиентом
                'waiting_list'
            ])->default('pending');

            // Финансы
            $table->unsignedInteger('amount'); // сумма в копейках
            $table->unsignedMediumInteger('order_id')
                ->nullable()
                ->index()
                ->comment('6-значный номер заказа');

            // Метаданные
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Для дополнительных данных

            // Таймстампы
            $table->timestamps();

            // Индексы
            $table->index(['master_class_id', 'payment_status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_class_bookings');
    }
};
