<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vietnamese_names_dictionary', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('element', ['kim', 'moc', 'thuy', 'hoa', 'tho']);
            $table->enum('gender', ['male', 'female', 'unisex'])->default('unisex');
            $table->text('meaning');
            $table->timestamps();

            $table->index(['element', 'gender']);
            $table->unique(['name', 'gender']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vietnamese_names_dictionary');
    }
};

