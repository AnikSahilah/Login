<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Tambahkan baris ini
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = 'password123'; // Ganti dengan password yang ingin Anda gunakan

        DB::table('users')->insert([
            'name' => 'Min Yoongi',
            'email' => 'myg@gmail.com',
            'password' => Hash::make($password),
        ]);
    }
}