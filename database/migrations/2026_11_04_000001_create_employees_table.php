<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('nip', 32)->primary();
            $table->string('nama', 128);
            $table->string('pin_hash', 255)->comment('bcrypt cost 12; never expose');
            $table->tinyInteger('tier')->unsigned()->comment('1=staff 2=manager 3=exec');
            $table->boolean('aktif')->default(true);
            $table->tinyInteger('failed_count')->unsigned()->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE employees ADD CONSTRAINT chk_employees_tier CHECK (tier IN (1, 2, 3))');
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};