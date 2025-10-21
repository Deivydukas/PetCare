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
    Schema::create('animals', function (Blueprint $table) {
        $table->id(); // auto increment ID
        $table->string('name');
        $table->string('species')->nullable();
        $table->string('breed')->nullable();
        $table->integer('age')->nullable();
        $table->string('status')->default('available');
        $table->text('notes')->nullable();
        $table->timestamps(); // created_at, updated_at
    });
}

public function down(): void
{
    Schema::dropIfExists('animals');
}

};
