<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('jti', 24)->nullable();
            $table->string('nip', 32)->nullable();
            $table->string('event', 16);
            $table->string('query', 200)->nullable();
            $table->integer('hit_count')->nullable();
            $table->string('ip', 45)->nullable();
            $table->tinyInteger('tier')->nullable()->comment('tier karyawan saat event');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('audit_log', function (Blueprint $table) {
            $table->index(['nip', 'created_at'], 'idx_audit_nip_time');
            $table->index('jti', 'idx_audit_jti');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};