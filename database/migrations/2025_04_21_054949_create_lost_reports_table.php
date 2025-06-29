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
        Schema::create('lost_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_user_id')->constrained('site_users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('book_copy_id')->constrained('book_copies')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('borrowing_id')->nullable()->constrained('borrowings')->onDelete('set null')->onUpdate('cascade');
            $table->timestamp('report_date')->useCurrent();
            $table->enum('status', ['Reported', 'Verified', 'Resolved'])->default('Reported')->index();
            $table->foreignId('admin_user_id_verify')->nullable()->constrained('admin_users')->onDelete('set null')->onUpdate('cascade');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolution_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_reports', function (Blueprint $table) {
            if (Schema::hasColumn('lost_reports', 'site_user_id')) {
                try {
                    $table->dropForeign(['site_user_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('lost_reports', 'book_copy_id')) {
                try {
                    $table->dropForeign(['book_copy_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('lost_reports', 'borrowing_id')) {
                try {
                    $table->dropForeign(['borrowing_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('lost_reports', 'admin_user_id_verify')) {
                try {
                    $table->dropForeign(['admin_user_id_verify']);
                } catch (\Exception $e) { /* ignore */
                }
            }
        });
        Schema::dropIfExists('lost_reports');
    }
};
