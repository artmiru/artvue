<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gift_certificates', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Relationships
            $table->foreignId('user_id')->nullable()->constrained()->comment('ID получателя');

            // Certificate details
            $table->string('code')->unique()->comment('Уникальный код сертификата');
            $table->string('name')->comment('Имя на сертификате');
            $table->unsignedInteger('amount')->default(290000)->comment('Номинал в копейках');
            $table->unsignedTinyInteger('visits_total')->default(1)->comment('Всего доступно сессий');
            $table->unsignedTinyInteger('visits_used')->default(0)->comment('Использовано сессий');

            // Dates
            $table->dateTime('expiry_date')->comment('Дата истечения');

            // Purchaser info
            $table->string('purchaser_name')->comment('Имя покупателя');
            $table->string('purchaser_phone')->nullable()->comment('Телефон покупателя');
            $table->string('purchaser_email')->comment('Email покупателя');

            // Statuses
            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'refunded'
            ])->default('pending')->comment('Статус оплаты');

            $table->enum('status', [
                'active',
                'used',
                'expired',
                'cancelled'
            ])->default('active')->comment('Статус сертификата');

            // Additional info
            $table->text('notes')->nullable()->comment('Примечания');
            $table->boolean('is_sent')->default(false)->comment('Отправлен получателю');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index('user_id');
            $table->index('purchaser_phone');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gift_certificates');
    }
};
