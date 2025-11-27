<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sale_id');

            // 👉 BIKIN NULLABLE AGAR BISA nullOnDelete
            $table->unsignedBigInteger('product_id')->nullable();

            $table->integer('qty');
            $table->decimal('price', 15, 2);    // harga per item saat transaksi
            $table->decimal('subtotal', 15, 2); // qty * price

            $table->timestamps();

            $table->foreign('sale_id')
                  ->references('id')->on('sales')
                  ->onDelete('cascade');

            // 👉 UBAH restrictOnDelete() MENJADI nullOnDelete()
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::dropIfExists('sale_items');
    }
};
