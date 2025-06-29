<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\SiteUser;
use Database\Factories\BookCopyFactory;
use Database\Factories\BorrowingFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // AdminUser::truncate();
        // Category::truncate();
        // Author::truncate();
        // Publisher::truncate();
        // Book::truncate();
        // BookCopy::truncate();

        // Schema::enableForeignKeyConstraints();

        AdminUser::factory(1)->create();
        // SiteUser::factory(2)->create();

        $this->call([
        //     CategorySeeder::class,
        //     AuthorSeeder::class,
        //     PublisherSeeder::class,
        //     BookSeeder::class,
            SettingSeeder::class,
        //     // BookingSeeder::class,
        ]);
    }
}
