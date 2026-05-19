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
        Schema::create('borrows', function (Blueprint $table) {
            //Primary key    
            $table->id();
            //Foreign key
            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')->references('id')->on('users')->restrictOnDelete();
            $table->unsignedBigInteger('books_id');
            $table->foreign('books_id')->references('id')->on('books')->restrictOnDelete();
            $table->unsignedBigInteger('petugas_id');
            $table->foreign('petugas_id')->references('id')->on('users')->restrictOnDelete();

            $table->date('borrow_date');
            $table->date('due_date');
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
