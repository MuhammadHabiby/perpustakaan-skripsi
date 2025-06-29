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
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->unique()->constrained('borrowings')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->enum('status', ['Unpaid', 'Paid', 'Waived'])->default('Unpaid')->index();
            $table->timestamp('payment_date')->nullable();
            $table->foreignId('admin_user_id_paid')->nullable()->constrained('admin_users')->onDelete('set null')->onUpdate('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            if (Schema::hasColumn('fines', 'borrowing_id')) {
                try {
                    $table->dropForeign(['borrowing_id']);
                } catch (\Exception $e) { /* ignore */ }
            }
            if (Schema::hasColumn('fines', 'admin_user_id_paid')) {
                try {
                    $table->dropForeign(['admin_user_id_paid']);
                } catch (\Exception $e) { /* ignore */ }
            }
        });
        Schema::dropIfExists('fines');
    }
};
