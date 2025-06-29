<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $smkJurusan = [
            "Teknik Komputer dan Jaringan",
            "Rekayasa Perangkat Lunak",
            "Multimedia",
            "Akuntansi dan Keuangan Lembaga",
            "Otomatisasi dan Tata Kelola Perkantoran",
            "Bisnis Daring dan Pemasaran",
            "Teknik Kendaraan Ringan Otomotif",
            "Teknik dan Bisnis Sepeda Motor",
            "Desain Pemodelan dan Informasi Bangunan",
            "Teknik Pengelasan",
            "Agribisnis Tanaman Pangan dan Hortikultura",
            "Perhotelan",
            "Tata Boga",
            "Tata Busana",
            "Teknik Elektronika Industri"
        ];

        $smkMataPelajaranUmum = [
            "Bahasa Indonesia",
            "Bahasa Inggris",
            "Matematika",
            "Sejarah Indonesia",
            "Pendidikan Pancasila dan Kewarganegaraan",
            "Pendidikan Agama",
            "Penjaskes",
            "Simulasi dan Komunikasi Digital"
        ];

        $smkMataPelajaranProduktif = [
            "Dasar Program Keahlian",
            "Kompetensi Keahlian",
            "Pemrograman Dasar",
            "Sistem Komputer",
            "Komputer dan Jaringan Dasar",
            "Administrasi Infrastruktur Jaringan",
            "Administrasi Sistem Jaringan",
            "Teknologi Jaringan Berbasis Luas (WAN)",
            "Basis Data",
            "Pemodelan Perangkat Lunak",
            "Akuntansi Dasar",
            "Perbankan Dasar",
            "Ekonomi Bisnis",
            "Gambar Teknik Otomotif",
            "Teknologi Dasar Otomotif",
            "Dasar Listrik dan Elektronika",
            "Mekanika Teknik",
            "Ilmu Statika dan Tegangan",
            "Dasar-dasar Budidaya Tanaman",
            "Sanitasi Hygiene",
            "Ilmu Gizi",
            "Dasar Pola",
            "Teknik Dasar Kelistrikan"
        ];

        $kataKunciTopik = [
            "Instalasi",
            "Konfigurasi",
            "Pemeliharaan",
            "Analisis",
            "Perancangan",
            "Pengembangan",
            "Manajemen",
            "Operasional",
            "Produksi",
            "Layanan",
            "Keamanan",
            "Troubleshooting",
            "Pengujian",
            "Implementasi",
            "Standar"
        ];

        $tipeBuku = ["Buku Teks", "Modul", "Panduan Praktik", "Buku Ajar", "Buku Siswa", "Buku Guru"];
        $kelas = ["X", "XI", "XII"];
        $semester = ["Ganjil", "Genap"];

        $awalSinopsis = [
            "Buku ini dirancang untuk memenuhi kebutuhan pembelajaran siswa SMK kelas {kelas} jurusan {jurusan}.",
            "Materi dalam {tipeBuku} ini mencakup kompetensi dasar mata pelajaran {mapel} untuk kelas {kelas}.",
            "Pembahasan dalam buku ini difokuskan pada {topik} dalam konteks keahlian {jurusan}.",
            "Modul ini menyajikan materi esensial {mapel} untuk semester {semester} kelas {kelas}.",
            "Sebagai panduan belajar siswa kelas {kelas}, buku ini mengupas tuntas tentang {topik}."
        ];
        $pengembanganSinopsis = [
            "Disusun berdasarkan kurikulum terbaru ({kurikulum}) dengan pendekatan pembelajaran aktif.",
            "Setiap bab dilengkapi dengan tujuan pembelajaran, peta konsep, materi pokok, contoh soal, dan latihan.",
            "Menggunakan bahasa yang lugas dan mudah dipahami, disertai ilustrasi dan gambar yang relevan.",
            "Fokus pada pengembangan keterampilan praktis melalui tugas individu maupun kelompok.",
            "Materi disajikan secara sistematis, mulai dari konsep dasar hingga aplikasi tingkat lanjut."
        ];
        $penutupSinopsis = [
            "Diharapkan siswa dapat menguasai kompetensi {kompetensi1} dan {kompetensi2} setelah mempelajari buku ini.",
            "Buku ini sangat cocok sebagai sumber belajar utama maupun pendamping bagi siswa SMK.",
            "Semoga buku ini bermanfaat dalam mencetak lulusan SMK yang kompeten dan siap kerja.",
            "Dengan mempelajari modul ini, siswa diharapkan mampu menerapkan {topik} dalam dunia kerja.",
            "Dilengkapi dengan soal evaluasi dan glosarium untuk mempermudah pemahaman."
        ];
        $kurikulumList = ["Kurikulum Merdeka", "Kurikulum 2013 Revisi", "Spektrum Keahlian SMK"];
        $kompetensiList = [
            "menginstalasi sistem operasi",
            "mengkonfigurasi router",
            "membuat aplikasi web sederhana",
            "menyusun laporan keuangan",
            "mengelola arsip kantor",
            "melakukan pemasaran digital",
            "melakukan servis berkala mobil",
            "mendiagnosis kerusakan sepeda motor",
            "membuat gambar kerja bangunan",
            "melakukan teknik pengelasan dasar",
            "membudidayakan tanaman sayur",
            "memberikan layanan 'front office'",
            "membuat hidangan kontinental",
            "membuat pola busana",
            "merakit rangkaian elektronika"
        ];

        $categoryIds = Category::whereIn('name', array_merge($smkJurusan, $smkMataPelajaranUmum))
            ->pluck('id')->toArray();
        $authorIds = Author::pluck('id')->toArray();
        $publisherIds = Publisher::pluck('id')->toArray();

        if (empty($categoryIds)) {
            Log::warning('BookSeeder: Tidak ada Kategori SMK yang ditemukan. Menggunakan null.');
            $categoryIds = [null];
        }
        if (empty($authorIds)) {
            Log::warning('BookSeeder: Tidak ada Author yang ditemukan. Menggunakan null.');
            $authorIds = [null];
        }
        if (empty($publisherIds)) {
            Log::warning('BookSeeder: Tidak ada Publisher yang ditemukan. Menggunakan null.');
            $publisherIds = [null];
        }

        $publicCoverDir = 'covers';
        $absoluteCoverDir = storage_path('app/public/' . $publicCoverDir);

        if (!File::isDirectory($absoluteCoverDir)) {
            if (File::makeDirectory($absoluteCoverDir, 0755, true, true)) {
                Log::info("BookSeeder: Created directory: " . $absoluteCoverDir);
            } else {
                Log::error("BookSeeder: Failed to create directory: " . $absoluteCoverDir);
            }
        }
        if (!File::isWritable($absoluteCoverDir)) {
            Log::error("BookSeeder: Directory not writable: " . $absoluteCoverDir);
        }

        $usedTitles = [];
        $totalBooks = 20;

        Log::info('BookSeeder: Starting seeding process for ' . $totalBooks . ' SMK books...');

        for ($i = 0; $i < $totalBooks; $i++) {
            $title = '';
            $tryCount = 0;
            $selectedJurusan = $faker->randomElement($smkJurusan);
            $selectedMapel = $faker->randomElement(array_merge($smkMataPelajaranUmum, $smkMataPelajaranProduktif));
            $selectedKelas = $faker->randomElement($kelas);
            $selectedTipe = $faker->randomElement($tipeBuku);
            $selectedTopik = $faker->randomElement($kataKunciTopik) . ' ' . $faker->randomElement($smkMataPelajaranProduktif);

            do {
                $tipeJudul = mt_rand(1, 5);
                switch ($tipeJudul) {
                    case 1:
                        $title = $selectedMapel . ' untuk SMK/MAK Kelas ' . $selectedKelas;
                        break;
                    case 2:
                        $title = $selectedTipe . ' ' . $selectedMapel . ' Kelas ' . $selectedKelas;
                        break;
                    case 3:
                        $title = $selectedTipe . ' ' . $selectedJurusan . ' Kelas ' . $selectedKelas;
                        break;
                    case 4:
                        $title = $selectedTopik . ' untuk ' . $selectedJurusan;
                        break;
                    case 5:
                        if ($selectedTipe === 'Modul') {
                            $title = $selectedTipe . ' ' . $selectedMapel . ' Kelas ' . $selectedKelas . ' Semester ' . $faker->randomElement($semester);
                        } else {
                            $title = $selectedMapel . ' Kelas ' . $selectedKelas;
                        }
                        break;
                }
                $title = Str::title($title);
                $tryCount++;
            } while (in_array($title, $usedTitles) && $tryCount < 10);

            if (in_array($title, $usedTitles)) {
                Log::warning("BookSeeder: Gagal membuat judul unik setelah 10 percobaan. Melewati iterasi {$i}.");
                continue;
            }
            $usedTitles[] = $title;

            $selectedKurikulum = $faker->randomElement($kurikulumList);
            $selectedKompetensi1 = $faker->randomElement($kompetensiList);
            $selectedKompetensi2 = $faker->randomElement(array_diff($kompetensiList, [$selectedKompetensi1]));

            $sinopsis = $faker->randomElement($awalSinopsis) . ' ' .
                $faker->randomElement($pengembanganSinopsis) . ' ' .
                $faker->randomElement($penutupSinopsis);

            $sinopsis = str_replace(
                ['{kelas}', '{jurusan}', '{tipeBuku}', '{mapel}', '{topik}', '{semester}', '{kurikulum}', '{kompetensi1}', '{kompetensi2}'],
                [$selectedKelas, $selectedJurusan, $selectedTipe, $selectedMapel, $selectedTopik, $faker->randomElement($semester), $selectedKurikulum, $selectedKompetensi1, $selectedKompetensi2],
                $sinopsis
            );

            $coverImagePath = null;
            try {
                $externalImageUrl = $faker->imageUrl(640, 480, 'book', true);
                $fileContents = @file_get_contents($externalImageUrl);

                if ($fileContents === false) {
                    $fallbackText = Str::limit($title, 25);
                    $externalImageUrl = 'https://placehold.co/640x480.png?text=' . urlencode($fallbackText);
                    $fileContents = @file_get_contents($externalImageUrl);
                }

                if ($fileContents === false) {
                    Log::error("BookSeeder: Failed to download image content from both sources for title: " . $title);
                } else {
                    $imageInfo = @getimagesizefromstring($fileContents);
                    $extension = 'png';
                    if ($imageInfo && isset($imageInfo['mime'])) {
                        $mime = $imageInfo['mime'];
                        $mimeMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                        if (isset($mimeMap[$mime])) {
                            $extension = $mimeMap[$mime];
                        }
                    }
                    $filename = Str::random(15) . '.' . $extension;
                    $filePath = $absoluteCoverDir . '/' . $filename;
                    if (file_put_contents($filePath, $fileContents) !== false) {
                        $coverImagePath = $publicCoverDir . '/' . $filename;
                    } else {
                        Log::error("BookSeeder: Failed to save image file to: " . $filePath);
                    }
                }
            } catch (\Exception $e) {
                Log::error("BookSeeder: Exception during image processing for title '{$title}': " . $e->getMessage());
            }


            $matchedCategory = Category::where('name', $selectedMapel)->first();
            if (!$matchedCategory) {
                $matchedCategory = Category::where('name', $selectedJurusan)->first();
            }
            $categoryId = $matchedCategory ? $matchedCategory->id : $faker->randomElement($categoryIds);


            try {
                $book = Book::create([
                    'title'             => $title,
                    'category_id'       => $categoryId,
                    'author_id'         => $faker->randomElement($authorIds),
                    'publisher_id'      => $faker->randomElement($publisherIds),
                    'isbn'              => $faker->unique()->isbn13,
                    'publication_year'  => $faker->numberBetween(2015, date('Y')),
                    'synopsis'          => $sinopsis,
                    'cover_image'       => $coverImagePath,
                    'location'          => 'Rak ' . $faker->randomElement(['A', 'B', 'C', 'D']) . $faker->numberBetween(1, 20) . '-' . $faker->numberBetween(1, 5),
                ]);

                $jumlahCopy = $faker->numberBetween(1, 5);
                for ($j = 0; $j < $jumlahCopy; $j++) {
                    BookCopy::create([
                        'book_id'   => $book->id,
                        'copy_code' => 'SMK' . $selectedKelas . '-' . strtoupper(Str::random(7)),
                        'status'    => $faker->randomElement(['Available']),
                        'condition' => $faker->randomElement(['Good', 'Fair', 'Poor']),
                    ]);
                }
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error("BookSeeder: Database error seeding book '{$title}': " . $e->getMessage());
                if (Str::contains($e->getMessage(), 'Unique violation') && Str::contains($e->getMessage(), 'isbn')) {
                    $faker->unique(true);
                    Log::info("BookSeeder: Resetting unique ISBN generator.");
                    $i--;
                    unset($usedTitles[array_search($title, $usedTitles)]);
                }
            } catch (\Exception $e) {
                Log::error("BookSeeder: General error seeding book '{$title}': " . $e->getMessage());
            }
        }
        $faker->unique(true);
        Log::info('BookSeeder: Seeding process finished. Total books attempted: ' . $totalBooks);
    }
}
