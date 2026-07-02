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
        Schema::create('giveaways', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('games',45);
            $table->string('prize',30);
            $table->boolean('status')->default(false);
            $table->timestamp('started_at')->useCurrent();
            $table->dateTime('finished_at');
            $table->integer('Entries')->default(0);
            $table->integer('Winners')->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giveaways');
    }
};
