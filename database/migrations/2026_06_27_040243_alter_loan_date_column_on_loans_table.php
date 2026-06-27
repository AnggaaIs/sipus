<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dateTime('loan_date')->change();
        });

        DB::statement('
            UPDATE loans
            SET loan_date = TIMESTAMP(DATE(loan_date), TIME(COALESCE(created_at, loan_date)))
            WHERE loan_date IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->date('loan_date')->change();
        });
    }
};
