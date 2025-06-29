<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $smkCategories = [
            ['name' => 'Teknik Komputer dan Jaringan', 'description' => 'Buku terkait instalasi, konfigurasi, dan pemeliharaan jaringan komputer.'],
            ['name' => 'Rekayasa Perangkat Lunak', 'description' => 'Buku mengenai pengembangan, pengujian, dan pemeliharaan software.'],
            ['name' => 'Multimedia', 'description' => 'Buku tentang desain grafis, animasi, audio-video editing.'],
            ['name' => 'Teknik Kendaraan Ringan Otomotif', 'description' => 'Buku mengenai perawatan, perbaikan, dan teknologi mobil.'],
            ['name' => 'Teknik dan Bisnis Sepeda Motor', 'description' => 'Buku mengenai perawatan, perbaikan, dan bisnis sepeda motor.'],
            ['name' => 'Desain Pemodelan dan Informasi Bangunan', 'description' => 'Buku tentang gambar teknik, desain bangunan, dan software CAD.'],
            ['name' => 'Teknik Pengelasan', 'description' => 'Buku mengenai berbagai teknik dan standar pengelasan logam.'],
            ['name' => 'Teknik Elektronika Industri', 'description' => 'Buku tentang rangkaian elektronika, mikrokontroler, dan sistem otomatisasi.'],
            ['name' => 'Teknik Instalasi Tenaga Listrik', 'description' => 'Buku mengenai instalasi, pemeliharaan, dan perbaikan sistem kelistrikan.'],
            ['name' => 'Teknik Permesinan', 'description' => 'Buku tentang penggunaan mesin bubut, frais, CNC, dan manufaktur.'],

            ['name' => 'Akuntansi dan Keuangan Lembaga', 'description' => 'Buku tentang siklus akuntansi, perpajakan, dan manajemen keuangan.'],
            ['name' => 'Otomatisasi dan Tata Kelola Perkantoran', 'description' => 'Buku mengenai manajemen kearsipan, korespondensi, dan teknologi perkantoran.'],
            ['name' => 'Bisnis Daring dan Pemasaran', 'description' => 'Buku tentang strategi marketing online, e-commerce, dan manajemen bisnis digital.'],
            ['name' => 'Manajemen Logistik', 'description' => 'Buku mengenai pengelolaan rantai pasok, pergudangan, dan distribusi barang.'],
            ['name' => 'Perbankan Syariah', 'description' => 'Buku tentang prinsip, produk, dan operasional perbankan berbasis syariah.'],

            ['name' => 'Perhotelan', 'description' => 'Buku mengenai layanan front office, housekeeping, dan manajemen hotel.'],
            ['name' => 'Tata Boga', 'description' => 'Buku tentang teknik memasak, resep masakan nusantara & internasional, dan manajemen dapur.'],
            ['name' => 'Usaha Perjalanan Wisata', 'description' => 'Buku tentang perencanaan tur, ticketing, guiding, dan manajemen travel agent.'],

            ['name' => 'Tata Busana', 'description' => 'Buku tentang desain mode, pembuatan pola, teknik menjahit, dan tekstil.'],
            ['name' => 'Desain Komunikasi Visual', 'description' => 'Buku tentang prinsip desain, tipografi, ilustrasi, dan branding.'], 
            ['name' => 'Seni Musik Klasik', 'description' => 'Buku mengenai teori musik, sejarah musik, dan praktik instrumen klasik.'],

            ['name' => 'Agribisnis Tanaman Pangan dan Hortikultura', 'description' => 'Buku tentang budidaya, pemeliharaan, dan bisnis tanaman pangan serta hortikultura.'],
            ['name' => 'Agribisnis Ternak Ruminansia', 'description' => 'Buku mengenai pemeliharaan, kesehatan, dan bisnis ternak sapi, kambing, dll.'],
            ['name' => 'Teknologi Pengolahan Hasil Pertanian', 'description' => 'Buku tentang teknik pengawetan, pengemasan, dan pengolahan produk pertanian.'],

             ['name' => 'Farmasi Klinis dan Komunitas', 'description' => 'Buku mengenai obat-obatan, pelayanan farmasi, dan kesehatan masyarakat.'],
             ['name' => 'Asisten Keperawatan', 'description' => 'Buku dasar-dasar ilmu keperawatan dan bantuan pasien.'],
             ['name' => 'Teknologi Laboratorium Medik', 'description' => 'Buku tentang analisis sampel medis dan penggunaan alat laboratorium.'],

            ['name' => 'Bahasa Indonesia', 'description' => 'Buku pelajaran Bahasa Indonesia untuk tingkat SMA/SMK.'],
            ['name' => 'Bahasa Inggris', 'description' => 'Buku pelajaran Bahasa Inggris untuk tingkat SMA/SMK, termasuk English for Specific Purposes.'],
            ['name' => 'Matematika', 'description' => 'Buku pelajaran Matematika umum dan terapan untuk SMK.'],
            ['name' => 'Sejarah Indonesia', 'description' => 'Buku pelajaran Sejarah Indonesia.'],
            ['name' => 'Pendidikan Pancasila dan Kewarganegaraan', 'description' => 'Buku pelajaran PPKn.'],
            ['name' => 'Pendidikan Agama dan Budi Pekerti', 'description' => 'Buku pelajaran Pendidikan Agama sesuai kurikulum.'], 
            ['name' => 'Pendidikan Jasmani Olahraga dan Kesehatan', 'description' => 'Buku pelajaran PJOK.'],
            ['name' => 'Seni Budaya', 'description' => 'Buku pelajaran Seni Budaya (Seni Rupa, Musik, Tari, Teater).'],
            ['name' => 'Prakarya dan Kewirausahaan', 'description' => 'Buku pelajaran PKWU yang mengembangkan kreativitas dan jiwa usaha.'],
            ['name' => 'Simulasi dan Komunikasi Digital', 'description' => 'Buku dasar penggunaan teknologi informasi dan komunikasi.'],
            ['name' => 'Fisika', 'description' => 'Buku pelajaran Fisika dasar dan terapan untuk SMK.'],
            ['name' => 'Kimia', 'description' => 'Buku pelajaran Kimia dasar dan terapan untuk SMK.'],
            ['name' => 'Biologi', 'description' => 'Buku pelajaran Biologi dasar dan terapan (terutama untuk jurusan Agribisnis/Kesehatan).'],
            ['name' => 'Dasar Program Keahlian', 'description' => 'Kategori umum untuk buku-buku dasar suatu program keahlian.'], 
            ['name' => 'Kompetensi Keahlian', 'description' => 'Kategori umum untuk buku-buku yang fokus pada kompetensi spesifik suatu keahlian.'],
        ];

        echo "Seeding categories...\n";
        $count = 0;
        foreach ($smkCategories as $data) {
            $existingCategory = Category::where('name', $data['name'])->first();

            if (!$existingCategory) {
                try {
                    Category::create([
                        'name'        => $data['name'],
                        'slug'        => Str::slug($data['name']),
                        'description' => $data['description'],
                    ]);
                     $count++;
                } catch (\Exception $e) {
                     echo "Error creating category '" . $data['name'] . "': " . $e->getMessage() . "\n";
                }
            } else {
            }
        }
        echo "Finished seeding categories. Created " . $count . " new categories.\n";
    }
}