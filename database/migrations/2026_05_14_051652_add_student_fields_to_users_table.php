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
        Schema::table('users', function (Blueprint $table) {
            // Identitas user
            $table->string('nisn', 10)->unique()->nullable()->after('id');
            $table->string('full_name')->nullable()->after('nisn');

            // Role
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');

            // Status akun - pending = belum disetujui admin
            $table->enum('account_status', ['pending', 'active', 'rejected', 'suspended'])
                ->default('pending')->after('role');

            $table->text('rejection_reason')->nullable()->after('account_status');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Info tambahan
            $table->string('class')->nullable()->after('approved_by'); // Kelas, misal: XII IPA 1
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
