<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->string('order_reference')->index();
            $table->string('method', 32)->index();
            $table->string('status', 32)->default('pending')->index();
            $table->unsignedBigInteger('amount_cents');
            $table->char('currency', 3)->default('EGP');
            $table->string('gateway_reference')->nullable()->index();
            $table->string('gateway_transaction_id')->nullable()->index();
            $table->text('gateway_response')->nullable();
            $table->text('callback_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index(['order_reference', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
