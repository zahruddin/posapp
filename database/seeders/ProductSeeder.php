<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $products = [
            ['Original Tea Mini', 3000],
            ['Original Tea Jumbo', 4000],
            ['Milk Tea Mini', 3500],
            ['Milk Tea Jumbo', 4500],
            ['Lemon Tea', 4000],
            ['Milo Tea', 4500],
            ['Chocolatos Tea', 4000],
            ['Macha Tea', 4000],
            ['Beng Beng Tea', 4500],
            ['GD Freeze Tea', 4000],
            ['GD Capucino Tea', 4500],
            ['Gula Aren Tea', 4000],
            ['Nutri Sari Jeruk Peras', 3000],
            ['Nutri Sari Milky Orange', 3500],
            ['Chocolatos Coklat', 3500],
            ['Chocolatos Macha', 3500],
            ['GD Freeaze', 4000],
            ['GD Capucino', 4000],
            ['Beng Beng', 4000],
            ['Kuku Bima Anggur', 3000],
            ['Kuku Bima Mangga', 3000],
            ['Extra Joss', 2500],
            ['Anggur Susu', 3500],
            ['Mangga Susu', 3500],
            ['Joss Susu', 3000],
        ];

        foreach ($products as $item) {
            Product::create([
                'id_outlet' => 9,
                'nama_produk' => $item[0],
                'gambar' => null,
                'harga_produk' => $item[1],
                'stok_produk' => 1000,
                'deskripsi' => 'Minuman segar pilihan terbaik.',
                'status' => 'aktif',
            ]);
        }
    }
}
