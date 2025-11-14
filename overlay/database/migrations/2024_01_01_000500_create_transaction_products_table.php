<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transaction_products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('transaction_id')->constrained();
            $t->foreignId('product_id')->constrained();
            $t->unsignedInteger('quantity');
            $t->timestamps();
            $t->unique(['transaction_id','product_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('transaction_products');
    }
};
