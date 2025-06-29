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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('author_id')->nullable()->constrained('authors')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('publisher_id')->nullable()->constrained('publishers')->onDelete('set null')->onUpdate('cascade');
            $table->string('isbn', 20)->nullable()->unique();
            $table->year('publication_year')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('location', 100)->nullable();
            $table->string('slug')->unique();
            $table->timestamps();

            $table->fullText(['title', 'synopsis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropFullText(['title', 'synopsis']);

            if (Schema::hasColumn('books', 'category_id')) { // Check if column exists
                try {
                    $table->dropForeign(['category_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('books', 'author_id')) {
                try {
                    $table->dropForeign(['author_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
            if (Schema::hasColumn('books', 'publisher_id')) {
                try {
                    $table->dropForeign(['publisher_id']);
                } catch (\Exception $e) { /* ignore */
                }
            }
        });
        Schema::dropIfExists('books');
    }
};
