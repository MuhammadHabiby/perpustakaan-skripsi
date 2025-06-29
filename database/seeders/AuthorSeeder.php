<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Log;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $specificAuthors = [
            [
                'name' => 'Tim Penyusun Kemdikbudristek',
                'bio' => 'Tim penyusun buku pelajaran dari Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi Republik Indonesia.'
            ],
            [
                'name' => 'Pusat Kurikulum dan Perbukuan',
                'bio' => 'Lembaga di bawah Kemdikbudristek yang bertanggung jawab atas pengembangan kurikulum dan perbukuan nasional.'
            ],
            [
                'name' => 'Direktorat Pembinaan SMK',
                'bio' => 'Direktorat Jenderal Pendidikan Vokasi, Kemdikbudristek, fokus pada pengembangan Sekolah Menengah Kejuruan.'
            ],
            [
                'name' => 'Tim Guru Produktif SMK',
                'bio' => 'Kelompok guru-guru dari berbagai SMK yang berkolaborasi menyusun materi ajar kejuruan.'
            ],
            [
                'name' => 'Tim Quantum Book',
                'bio' => 'Tim penulis buku-buku pendidikan dan referensi.'
            ],
        ];

        $createdCount = 0;
        Log::info('Seeding specific authors...');
        foreach ($specificAuthors as $authorData) {
            $existing = Author::where('name', $authorData['name'])->first();
            if (!$existing) {
                try {
                    Author::create([
                        'name' => $authorData['name'],
                        'bio'  => $authorData['bio'],
                    ]);
                    $createdCount++;
                } catch (\Exception $e) {
                    Log::error("Error creating specific author '{$authorData['name']}': " . $e->getMessage());
                }
            }
        }
        Log::info("Finished seeding specific authors. Created {$createdCount} new specific authors.");


        $fakerAuthorsToCreate = 15;
        $fakerCreatedCount = 0;
        Log::info("Seeding {$fakerAuthorsToCreate} random authors...");
        for ($i = 0; $i < $fakerAuthorsToCreate; $i++) {
            $authorName = $faker->unique()->name;
            $existing = Author::where('name', $authorName)->first();
            if (!$existing) {
                try {
                    Author::create([
                        'name' => $authorName,
                        'bio'  => $faker->sentence(10)
                    ]);
                    $fakerCreatedCount++;
                } catch (\Exception $e) {
                    Log::error("Error creating faker author '{$authorName}': " . $e->getMessage());
                    $faker->unique(true);
                }
            }
        }
        $faker->unique(true);
        Log::info("Finished seeding random authors. Created {$fakerCreatedCount} new random authors.");
        Log::info("AuthorSeeder finished. Total new authors created: " . ($createdCount + $fakerCreatedCount));
    }
}
