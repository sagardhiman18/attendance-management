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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emp_id');
            $table->date('date');
            $table->string('day');
            $table->enum('status', ['absent', 'present', 'lwp', 'paid_leave'])->default('present');
            $table->timestamps();

            $table->foreign('emp_id')->references('id')->on('employees');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
