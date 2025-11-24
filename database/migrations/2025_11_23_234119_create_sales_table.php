<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');            // kasir yang input
            $table->unsignedBigInteger('customer_id')->nullable(); // pelanggan (wajib kalau kasbon)

            $table->decimal('total_amount', 15, 2);           // total harga semua item
            $table->decimal('paid_amount', 15, 2)->default(0);// uang yang dibayar
            $table->decimal('change_amount', 15, 2)->default(0); // kembalian (kalau ada)

            $table->string('status'); // 'paid' atau 'kasbon'
            $table->string('payment_method')->nullable(); // cash, transfer, dll
            $table->string('customer_name_snapshot')->nullable(); // salinan nama pelanggan saat transaksi
            $table->text('note')->nullable();               // catatan tambahan

            $table->timestamps();

            // FK
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['customer_id']);
        });

        Schema::dropIfExists('sales');
    }
};
