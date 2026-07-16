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
        Schema::table('books', function (Blueprint $table): void {
            $table->dropForeign(['ddc_id']);
            $table->dropForeign(['publisher_id']);

            $table->foreign('ddc_id')->references('id')->on('ddcs')->restrictOnDelete();
            $table->foreign('publisher_id')->references('id')->on('publishers')->restrictOnDelete();
        });

        Schema::table('book_author', function (Blueprint $table): void {
            $table->dropForeign(['author_id']);

            $table->foreign('author_id')->references('id')->on('authors')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->dropForeign(['ddc_id']);
            $table->dropForeign(['publisher_id']);

            $table->foreign('ddc_id')->references('id')->on('ddcs')->nullOnDelete();
            $table->foreign('publisher_id')->references('id')->on('publishers')->nullOnDelete();
        });

        Schema::table('book_author', function (Blueprint $table): void {
            $table->dropForeign(['author_id']);

            $table->foreign('author_id')->references('id')->on('authors')->cascadeOnDelete();
        });
    }
};
