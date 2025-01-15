<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_report_id')->constrained('order_reports')->onDelete('cascade');
            $table->integer('product_id');
            $table->integer('total_sold');
            $table->decimal('revenue', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reports');
    }
}; 