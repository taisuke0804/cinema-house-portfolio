<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('screening_id')->constrained()->cascadeOnDelete(); // 上映に紐付く
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // ユーザーに紐付く
            $table->string('row');  // 行 (例: A, B, C)
            $table->unsignedInteger('number'); // 座席番号
            $table->boolean('is_reserved')->default(false); // 予約済みかどうか
            $table->timestamps();

            // 同じ上映で同じ座席は重複不可
            $table->unique(['screening_id', 'row', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
