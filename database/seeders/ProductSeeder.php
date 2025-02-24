<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'id_outlet' => 1,
                'nama_produk' => 'Es Teh Manis',
                'harga_produk' => 5000,
                'stok_produk' => 50,
                'deskripsi' => 'Es teh manis segar dengan gula alami.',
                'gambar' => 'produk/es_teh.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Es Jeruk',
                'harga_produk' => 7000,
                'stok_produk' => 30,
                'deskripsi' => 'Es jeruk segar dari jeruk asli.',
                'gambar' => 'produk/es_jeruk.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Kopi Hitam',
                'harga_produk' => 8000,
                'stok_produk' => 40,
                'deskripsi' => 'Kopi hitam tanpa gula, cocok untuk pecinta kopi.',
                'gambar' => 'produk/kopi_hitam.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Cappuccino',
                'harga_produk' => 12000,
                'stok_produk' => 25,
                'deskripsi' => 'Cappuccino lembut dengan aroma khas.',
                'gambar' => 'produk/cappuccino.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Lemon Tea',
                'harga_produk' => 9000,
                'stok_produk' => 35,
                'deskripsi' => 'Kombinasi teh dan lemon yang menyegarkan.',
                'gambar' => 'produk/lemon_tea.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Green Tea Latte',
                'harga_produk' => 15000,
                'stok_produk' => 20,
                'deskripsi' => 'Teh hijau dengan susu dan rasa khas.',
                'gambar' => 'produk/green_tea_latte.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Milo Dingin',
                'harga_produk' => 10000,
                'stok_produk' => 30,
                'deskripsi' => 'Minuman cokelat Milo dengan es batu.',
                'gambar' => 'produk/milo_dingin.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Thai Tea',
                'harga_produk' => 11000,
                'stok_produk' => 25,
                'deskripsi' => 'Thai Tea khas Thailand dengan susu.',
                'gambar' => 'produk/thai_tea.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Jus Alpukat',
                'harga_produk' => 13000,
                'stok_produk' => 20,
                'deskripsi' => 'Jus alpukat dengan susu kental manis.',
                'gambar' => 'produk/jus_alpukat.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Jus Mangga',
                'harga_produk' => 12000,
                'stok_produk' => 20,
                'deskripsi' => 'Jus mangga segar dari buah pilihan.',
                'gambar' => 'produk/jus_mangga.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Jus Jambu',
                'harga_produk' => 11000,
                'stok_produk' => 30,
                'deskripsi' => 'Jus jambu merah kaya vitamin C.',
                'gambar' => 'produk/jus_jambu.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Bubble Tea',
                'harga_produk' => 16000,
                'stok_produk' => 15,
                'deskripsi' => 'Bubble tea dengan topping pearl yang kenyal.',
                'gambar' => 'produk/bubble_tea.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Susu Coklat',
                'harga_produk' => 10000,
                'stok_produk' => 25,
                'deskripsi' => 'Susu coklat dengan rasa manis yang pas.',
                'gambar' => 'produk/susu_coklat.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Milkshake Vanilla',
                'harga_produk' => 14000,
                'stok_produk' => 20,
                'deskripsi' => 'Milkshake dengan rasa vanilla yang creamy.',
                'gambar' => 'produk/milkshake_vanilla.jpg',
                'status' => 'aktif',
            ],
            [
                'id_outlet' => 1,
                'nama_produk' => 'Air Mineral',
                'harga_produk' => 3000,
                'stok_produk' => 50,
                'deskripsi' => 'Air mineral kemasan botol 600ml.',
                'gambar' => 'produk/air_mineral.jpg',
                'status' => 'aktif',
            ],
        ];

        DB::table('products')->insert($products);
    }
}
