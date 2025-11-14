<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained();
            $t->foreignId('gateway_id')->nullable()->constrained();
            $t->string('external_id')->nullable();
            $t->enum('status', ['approved','declined','error','refunded'])->default('declined');
            $t->unsignedBigInteger('amount');
            $t->string('card_last_numbers', 4)->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};
