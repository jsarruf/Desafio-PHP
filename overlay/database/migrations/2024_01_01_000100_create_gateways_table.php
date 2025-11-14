<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gateways', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('priority')->default(1);
            $t->json('config')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('gateways');
    }
};
