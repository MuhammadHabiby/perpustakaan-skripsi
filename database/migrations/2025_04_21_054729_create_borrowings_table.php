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
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_user_id')->constrained('site_users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('book_copy_id')->constrained('book_copies')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('admin_user_id_loan')->constrained('admin_users')->onDelete('restrict')->onUpdate('cascade');
            $table->date('borrow_date');
            $table->date('due_date')->index();
            $table->date('return_date')->nullable()->index();
            $table->enum('status', ['Borrowed', 'Returned', 'Overdue', 'Lost'])->default('Borrowed')->index();
            $table->foreignId('admin_user_id_return')->nullable()->constrained('admin_users')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (Schema::hasColumn('borrowings', 'site_user_id')) {
                try {
                    $table->dropForeign(['site_user_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('borrowings', 'book_copy_id')) {
                try {
                    $table->dropForeign(['book_copy_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('borrowings', 'booking_id')) {
                try {
                    $table->dropForeign(['booking_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('borrowings', 'admin_user_id_loan')) {
                try {
                    $table->dropForeign(['admin_user_id_loan']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('borrowings', 'admin_user_id_return')) {
                try {
                    $table->dropForeign(['admin_user_id_return']);
                } catch (\Exception $e) { /* ignore */
                }
            }
        });
        Schema::dropIfExists('borrowings');
    }
};
