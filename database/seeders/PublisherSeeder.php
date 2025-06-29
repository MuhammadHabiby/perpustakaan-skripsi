<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Publisher;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Log;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $publishers = [
            'Erlangga',
            'Yudhistira',
            'Gramedia Pustaka Utama',
            'Penerbit Andi',
            'Grafindo Media Pratama',
            'Bumi Aksara',
            'Pusat Kurikulum dan Perbukuan',
            'Tiga Serangkai Pustaka Mandiri',
            'Penerbit Duta',
            'ESIS (Erlangga Straight Step Series)',
            'Armico Bandung',
            'Penerbit Quadra',
            'Intan Pariwara',
            'Mediatama',
            'CV. Media Karya Putra',
            'Ganeca Exact',
            'Penerbit Galaxy',
            'Penerbit Platinum',
            'Penerbit Saka Mitra Kompetensi',
            'Penerbit Deepublish',
        ];

        $createdCount = 0;
        Log::info('Seeding specific publishers...');
        foreach ($publishers as $publisherName) {
            $existing = Publisher::where('name', $publisherName)->first();
            if (!$existing) {
                try {
                    Publisher::create([
                        'name'    => $publisherName,
                        'address' => $faker->address,
                    ]);
                    $createdCount++;
                } catch (\Exception $e) {
                    Log::error("Error creating publisher '{$publisherName}': " . $e->getMessage());
                }
            }
        }
        Log::info("Finished seeding specific publishers. Created {$createdCount} new publishers.");
        Log::info("PublisherSeeder finished.");
    }
}
