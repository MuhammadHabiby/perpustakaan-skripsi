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
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('restrict')->onUpdate('cascade');
            $table->string('copy_code', 100)->unique();
            $table->enum('status', ['Available', 'Borrowed', 'Booked', 'Lost', 'Damaged', 'Maintenance'])->default('Available')->index();
            $table->enum('condition', ['Good', 'Fair', 'Poor'])->default('Good');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {
            if (Schema::hasColumn('book_copies', 'book_id')) {
                try {
                    $table->dropForeign(['book_id']);
                } catch (\Exception $e) { /* ignore */ }
            }
        });
        Schema::dropIfExists('book_copies');
    }
};
