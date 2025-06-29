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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_user_id')->constrained('site_users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('book_copy_id')->nullable()->constrained('book_copies')->nullOnDelete();
            $table->timestamp('booking_date')->useCurrent();
            $table->timestamp('expiry_date')->index()->nullable();
            $table->enum('status', ['Active', 'Expired', 'ConvertedToLoan', 'Cancelled'])->default('Active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'site_user_id')) {
                try {
                    $table->dropForeign(['site_user_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('bookings', 'book_id')) {
                try {
                    $table->dropForeign(['book_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            try {
                $table->dropForeign(['book_copy_id']);
            } catch (\Exception $e) {
            }
            $table->dropColumn('book_copy_id');
        });
        Schema::dropIfExists('bookings');
    }
};
